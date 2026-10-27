<?php
    $approval_url = API."?mode=toGetBluckApprovaldata";
    $approval_data = callAPI($approval_url);
    $approval_data = json_decode($approval_data,1);
    $gen_code = $approval_data['genc_code'];
    $count = $approval_data['count'];
    $users = $approval_data['user'];
    $status = $_GET['status'] != '' ? $_GET['status'] : '2';
    $transaction_type = $_GET['transaction_type'] != '' ? $_GET['transaction_type'] : 'requisitions';
    $list_url = API."?mode=togetbluckstatusdata&status=".$status."&transaction_type=".$transaction_type;
    $list_data = callAPI($list_url);
    $list_data = json_decode($list_data ,1);
    $sum_count = 0;
    foreach ($count as  $cc) {
        $sum_count+= array_sum($cc);
    }

?>
<script type="text/javascript">
    function ajaxview(rid,table) {
        jQuery.ajax({
            url: "ajax_bluk.php",
            data:'rid='+rid+'&table='+table,
            type: "POST",
            success:function(data){
                console.log(data);
                    $("#ajaxdisplay").html(data);
            },
            error:function (){}
        });
    }
    function submitstatus(table, status){

        var row =$('#row').val();
        var str ='';
        var sing ='';
        for(var i=1; i < row; i++){
            var sing ='';
            sing = $('#checked_'+i).val();
            if(sing == 1){
                var check = $('#check_'+i).val();
                str+= check+',';
            }
        }
        alert(str);
        if(str !=''){
            jQuery.ajax({
                url: "ajax.php",
                data:'page=blukapproval&rid='+str+'&table='+table+'&status='+status,
                type: "POST",
                success:function(data){
                    console.log(data);
                    if(data){
                        swal.fire({
                            title: "Record has been Updated successfully ",
                            text: "Do you want to close ? ",
                            type: "success",
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
                            confirmButtonClass: "btn btn-danger",
                            showCancelButton: false,
                            cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                            cancelButtonClass: "btn btn-default"
                        }).then(function(result) {
                            if (result.value) {
                                location.reload(true);
                            }
                        });

                    }
                },
                error:function (){}
            });
        }else{
            alert("Please Select At leaset one");
        }
    }
    function checkall(){
        var checkall =$('#checkall').val();
        var row =$('#row').val();
        if(checkall == 0){
            checkall =1;
            $("#uns").text(0);
            $("#sel").text(row-1);
        }else{
            checkall =0;
            $("#uns").text(row-1);
            $("#sel").text(0);
        }
        $('#checkall').val(checkall);
        for(var j=1; j<row;j++){
            if(checkall == 1){
                $("#check_"+j).prop("checked", true);
            }else{
                $("#check_"+j).prop("checked", false);
            }
            $("#checked_"+j).val(checkall);
        }
    }
    function singlecheck(id,val){
        var checkall =$('#checked_'+val).val();
        var uns = parseFloat($('#uns').text());
        var sel = parseFloat($('#sel').text());
        if(checkall == 0){
            checkall =1;
            $("#uns").text(uns-1);
            $("#sel").text(sel+1);
        }else{
            checkall =0;
            $("#uns").text(uns+1);
            $("#sel").text(sel-1);
        }
        $("#checked_"+val).val(checkall);
    }

</script>
<!-- begin:: Content -->
<div class="d-flex flex-column-fluid">
<div class="container-fluid">
    <!--Begin::Tasks-->
    <div class="kt-grid kt-grid--desktop kt-grid--ver-desktop  kt-todo" id="kt_todo">

        <!--Begin::Aside Mobile Toggle-->
        <button class="flex-row-auto offcanvas-mobile w-200px w-xxl-275px" id="kt_todo_aside">
            <i class="la la-close"></i>
        </button>
        <!--Begin:: Tasks Aside-->
        <div class="kt-grid__item   kt-portlet kt-todo__aside" id="kt_todo_aside_2">
            <div class="kt-todo__nav accordion accordion-solid accordion-toggle-plus">
                <ul class="card card-custom card-stretch">
                    <li class="kt-nav__item">
                        <a href="#" class="kt-nav__link" data-action="list" data-type="inbox">
                            <img style="width:25px; height:25px; margin:0 11px 0 0;" src="assets/media/users/300_16.jpg" alt="User Profile"> <span class="kt-nav__link-text"><?php echo $users[$_SESSION['userid']]['name'] ?></span>
                            </span>
                        </a>
                    </li>
                    <li class="kt-nav__item  kt-nav__item--active">
                        <a href="#" class="kt-nav__link" data-action="list" data-type="marked">
                            <svg xmlns="http:
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3" />
                                    <path d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z" fill="#000000" />
                                    <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000" />
                                </g>
                            </svg> <span class="kt-nav__link-text">My Tasks</span>
                            <span class="kt-nav__link-badge">
                                <span class="kt-badge kt-badge--unified-success kt-badge--md kt-badge--rounded kt-badge--boldest">
                                    <?php echo $sum_count;?>
                                </span>
                            </span>
                        </a>
                    </li>

                    <?php $sutittle = array();$sutittle2 = array();
                        $i=0; foreach ($approval_data['data'] as $approval) {
                            $sutittle[$approval['table']] = $approval['description']; ?>

                    <li class="kt-nav__item kt-margin-t-20 kt-margin-b-20 card-header" id="requisitions">
                        <div class="card-title collapsed" data-toggle="collapse" data-target="<?php echo '#'.$approval['table']; ?>" aria-expanded="true" aria-controls="<?php echo '#'.$approval['table']; ?>">
                            <span class="kt-nav__link-title"><?php echo  $approval['description']; ?></span>

                            <?php $main_count = array_sum($count[$approval['table']]) !='' ? array_sum($count[$approval['table']]) : 0;?>
                            <span class="kt-badge kt-badge--brand kt-badge--square float-right mrgn-less"><?php echo  $main_count; ?></span>
                        </div>
                    </li>
                    <li class="p-0 border-0" style="list-style:none;">
                        <div class="collapse" aria-labelledby="<?php echo $approval['table']; ?>" data-parent="<?php echo '#'.$approval['table'] ?>" id="<?php echo $approval['table']; ?>">
                            <ul class="kt-nav p-0">
                            <?php  foreach ($approval_data['status'][$approval['table']] as $status_progress) {
                                $sutittle2[$approval['table']][$status_progress['status_status_id']] = $status_progress['status_action_label']
                                ?>
                                <li class="kt-nav__item">
                                    <a href="index.php?option=COM_LIST_BLUK&transaction_type=<?php echo $approval['table']; ?>&status=<?php echo $status_progress['status_status_id']; ?>" class="kt-nav__link">
                                        <i class="kt-nav__link-icon fa fa-genderless kt-font-warning"></i>
                                        <span class="kt-nav__link-text"><?php echo $status_progress['status_action_label']; ?></span>
                                        <?php $count_data = $count[$approval['table']][$status_progress['status_status_id']] !='' ? $count[$approval['table']][$status_progress['status_status_id']] : 0 ; ?>
                                        <span class="kt-badge kt-badge--brand kt-badge--square"><?php echo $count_data; ?></span>
                                    </a>
                                </li>
                            <?php } ?>
                            </ul>
                        </div>
                    </li>
                    <?php $i++;
} ?>
                </ul>
            </div>
        </div>

        <!--End:: Tasks Aside-->

        <!--Begin:: Tasks Content-->
        <div class="kt-grid__item kt-grid__item--fluid kt-todo__content" id="kt_todo_content">
            <div class="kt-todo__tasks-top">
                <div class="kt-portlet">

                    <!--Begin:: Tasks Toolbar-->
                    <div class="kt-todo__header">
                        <h3 class="kt-todo__title"><?php echo $sutittle[$transaction_type]." - ". $sutittle2[$transaction_type][$status];  ?></h3>

                        <!--Begin:: Tasks Nav-->
                        <div class="kt-todo__nav">
                            <a href="custom/apps/todo/tasks.html" class="kt-todo__link kt-todo__link--active">Selected <span class="kt-badge kt-badge--brand kt-badge--square" id="sel">0</span></a>
                            <a href="custom/apps/todo/docs.html" class="kt-todo__link ">Unselected <span class="kt-badge kt-badge--primary kt-badge--square" id="uns" ><?php echo count((array)$list_data); ?></span></a>
                            <a href="custom/apps/todo/files.html" class="kt-todo__link ">Total <span class="kt-badge kt-badge--warning kt-badge--square" id="to"><?php echo count((array)$list_data); ?></span></a>

                        </div>

                        <!--End:: Tasks Nav-->

                        <!--Begin:: Tasks Users-->
                        <div class="kt-todo__users">

                            <a href="#" class="kt-media kt-media--xs  kt-bg-success kt-margin-l-10" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="Add Participants" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <span><i class="kt-nav__link-icon fa flaticon2-plus kt-font-light"></i></span>
                            </a>
                        </div>

                        <!--End:: Tasks Users-->
                    </div>

                    <!--End:: Tasks Toolbar-->
                </div>
            </div>
            <div class="kt-todo__tasks-bottom">
                <div class="row">
                    <div class="col-xl-7 col-md-7 col-lg-7">


                        <div class="kt-grid__item kt-grid__item--fluid  kt-portlet kt-portlet--height-fluid kt-todo__list" id="kt_todo_list">
                            <div class="kt-portlet__body kt-portlet__body--fit-x">
                                <div class="kt-todo__head">
                                    <div class="kt-todo__toolbar">
                                        <div class="kt-todo__actions kt-todo__actions--expanded">
                                            <div class="kt-todo__check">
                                                <label class="kt-checkbox kt-checkbox--single kt-checkbox--tick kt-checkbox--brand">
                                                    <input aria-label="Checkall" type="checkbox" name="checkall" onclick="checkall()" id="checkall" value="0" onkeydown="">

                                                    <span></span>
                                                </label>

                                                <?php $lists_status = toGetStatusProgressAction($transaction_type, $status);?>

                                            </div>
                                            <div class="kt-todo__panel" style="margin-left: 10px;">
                                                <?php foreach($lists_status as $list_status){?>
                                                 <button class="btn btn-label-success btn-upper btn-sm btn-bold" onclick="submitstatus(<?php echo "'".$transaction_type."'"; ?> , <?php echo "'".$list_status['status']."'"; ?>)" name="status" id="status" value="<?php echo $list_status['status']; ?>"><?php echo $list_status['action_label']; ?></button>
                                                <?php } ?>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="kt-todo__body">
                                    <div class="kt-todo__items" data-type="task">
                                        <?php $j=1; foreach ($list_data as $list){ ?>
                                        <div class="kt-todo__item kt-todo__item--unread">
                                            <div class="kt-todo__info">
                                                <div class="kt-todo__actions">
                                                    <label class="kt-checkbox kt-checkbox--single kt-checkbox--tick kt-checkbox--brand">
                                                        <input type="hidden" id="checked_<?php echo $j; ?>" value="0">
                                                        <input aria-label="Checkbox" type="checkbox" onclick="singlecheck(this.id,<?php echo $j; ?>)" id="check_<?php echo $j; ?>"  value="<?php echo  $list['transaction_id']; ?>" onkeydown="">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="kt-todo__details"  onclick="ajaxview(<?php echo $list['transaction_id'];  ?>,<?php echo"'".$list['transaction_type']."'";  ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                <div class="kt-todo__message">
                                                    <span class="kt-todo__subject"><?php echo $list['party_name']." - ".$list['transaction_number'] ;?></span>
                                                </div>
                                                <div class="kt-todo__labels">
                                                    <span class="kt-todo__label kt-badge kt-badge--unified-brand kt-badge--bold kt-badge--inline"><?php echo date('M d, Y',strtotime($list['transaction_date'])); ?></span>
                                                    <span class="kt-todo__label kt-badge kt-badge--unified-brand kt-badge--bold kt-badge--inline"><?php echo $gen_code[$list['type']]; ?></span>

                                                    <?php $opex_cpex = $list['transaction_number'] == "1" ? : 'Cpex' ; 'Opex';?>
                                                    <span class="kt-todo__label kt-badge kt-badge--unified-brand kt-badge--bold kt-badge--inline"><?php echo $opex_cpex; ?></span>
                                                    <span class="kt-todo__label kt-badge kt-badge--unified-brand kt-badge--bold kt-badge--inline"><?php echo customeummarmonyformate($list['total_amount']); ?></span>

                                                </div>
                                            </div>
                                            <div class="kt-todo__datetime" data-toggle="view">
                                                <?php $days = dateDiffInDays($list['request_date']);
                                                    if($days == 0){ echo "To day";
}
                                                    elseif($days < 7){ echo $days ." days ago";
}
                                                    else{ echo date('M d, Y',strtotime($list['request_date']));
}
                                                 ?>

                                            </div>
                                            <div class="kt-todo__sender" data-toggle="kt-tooltip" data-placement="top" title="" data-original-title="<?php echo $users[$list['originator']]['name']; ?>">
                                                <?php if($users[$list['originator']]['photo'] != ''){ ?>
                                                <span class="kt-media kt-media--sm kt-media--danger" style="background-image: url('uploads/all/<?php echo $users[$list['originator']]['photo']; ?>')">
                                                    <span></span>
                                                </span>
                                                <?php }else{ ?>
                                                    <span class="kt-media kt-media--sm kt-media--danger">
                                                        <span><?php echo strtoupper(substr($users[$list['originator']]['name'],0,1));?></span>
                                                    </span>
                                                </span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php $j++;
} ?>
                                        <input type="hidden" name="row" id="row" value="<?php echo $j; ?>">

                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>
                    <div class="col-xl-5 col-md-5 col-lg-5 pt-10 pt-xl-0">

                        <div class="kt-grid__item kt-grid__item--fluid  kt-portlet kt-portlet--height-fluid kt-todo__view" id="ajaxdisplay">
                        </div>
                        <!--End:: Inbox View-->
                    </div>
                </div>
            </div>
        </div>
        </div>

        <!--End:: Tasks Content-->
    </div>

    <!--End::Tasks-->
</div>
