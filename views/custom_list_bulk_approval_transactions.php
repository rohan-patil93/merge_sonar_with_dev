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
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Todo-->
        <div class="d-flex flex-row">
            <!--begin::Aside-->
            <div class="flex-row-auto offcanvas-mobile w-200px w-xxl-275px" id="kt_todo_aside">
                <!--begin::Card-->
                <div class="card card-custom card-stretch">
                    <!--begin::Body-->
                    <div class="card-body px-5">
                        <!--begin:Nav-->
                        <div class="navi navi-hover navi-active navi-link-rounded navi-bold navi-icon-center navi-light-icon">
                            <!--begin:Item-->
                            <div class="navi-item my-2">
                                <a href="#" class="navi-link active">
                                    <span class="navi-icon mr-4">
                                        <span class="svg-icon svg-icon-lg">

                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,13 C19,13.5522847 18.5522847,14 18,14 L6,14 C5.44771525,14 5,13.5522847 5,13 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M13.8,4 C13.1562,4 12.4033,4.72985286 12,5.2 C11.5967,4.72985286 10.8438,4 10.2,4 C9.0604,4 8.4,4.88887193 8.4,6.02016349 C8.4,7.27338783 9.6,8.6 12,10 C14.4,8.6 15.6,7.3 15.6,6.1 C15.6,4.96870845 14.9396,4 13.8,4 Z" fill="#000000" opacity="0.3" />
                                                    <path d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z" fill="#000000" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </span>
                                    <span class="navi-text font-weight-bolder font-size-lg">My Tasks</span>
                                    <span class="navi-label">
                                        <span class="label label-rounded label-light-success font-weight-bolder"><?php echo $sum_count;?></span>
                                    </span>
                                </a>
                            </div>
                            <!--end:Item-->
                            <!--begin:Item-->


                            <!--end:Item-->
                            <!--begin:Item-->

<div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3">
    <?php $sutittle = array();$sutittle2 = array();
                        $i=0; foreach ($approval_data['data'] as $approval) {
                            $sutittle[$approval['table']] = $approval['description']; ?>
<div class="card">
        <div class="card-header" id="headingTwo3">
            <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo<?php echo $i; ?>">
                <?php echo  $approval['description']; ?>
                <?php $main_count = array_sum($count[$approval['table']]) !='' ? array_sum($count[$approval['table']]) : 0;?>
                <div class="text-right">
                                        <span class="label label-rounded label-light-success float-right font-weight-bolder"><?php echo  $main_count; ?></span>
                                    </div>
            </div>
        </div>
        <div id="collapseTwo<?php echo $i; ?>" class="collapse" data-parent="#accordionExample3">
            <div class="card-body">
                <?php  foreach ($approval_data['status'][$approval['table']] as $status_progress) {
                                $sutittle2[$approval['table']][$status_progress['status_status_id']] = $status_progress['status_action_label']
                                ?>
                <div class="navi-item my-2">
                    <a href="index.php?option=COM_LIST_BULK_APPROVAL_TRANSACTIONS&transaction_type=<?php echo $approval['table']; ?>&status=<?php echo $status_progress['status_status_id']; ?>" class="navi-link">
                        <span class="navi-text font-weight-bolder font-size-lg"><?php echo $status_progress['status_action_label']; ?></span>
                        <?php $count_data = $count[$approval['table']][$status_progress['status_status_id']] !='' ? $count[$approval['table']][$status_progress['status_status_id']] : 0 ; ?>
                        <span class="navi-label">
                            <span class="label label-rounded label-light-warning font-weight-bolder"><?php echo $count_data; ?></span>
                        </span>
                    </a>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php $i++;
} ?>
</div>

                            <!--end:Item-->
                        </div>
                        <!--end:Nav-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Aside-->

            <div class="flex-row-fluid ml-lg-8">
                <div class="d-flex flex-column flex-grow-1">
                    <!--begin::Head-->
                    <div class="card card-custom gutter-b">
                        <!--begin::Body-->
                        <div class="card-body d-flex align-items-center justify-content-between flex-wrap py-3">
                            <!--begin::Info-->
                            <div class="d-flex align-items-center mr-2 py-2">
                                <!--begin::Title-->
                                <h3 class="font-weight-bold mb-0 mr-10"><?php echo $sutittle[$transaction_type]." - ". $sutittle2[$transaction_type][$status];  ?></h3>
                                <!--end::Title-->
                                <!--begin::Navigation-->
                                <div class="d-flex mr-3">
                                    <!--begin::Navi-->
                                    <div class="navi navi-hover navi-active navi-link-rounded navi-bold d-flex flex-row">
                                        <!--begin::Item-->
                                        <div class="navi-item mr-2">
                                            <a href="custom/apps/todo/tasks.html" class="navi-link active">
                                                <span class="navi-text">Selected  &nbsp;<span class="label label-rounded label-light-success font-weight-bolder" id="sel">0</span> </span>
                                            </a>
                                        </div>
                                        <!--end::Item-->
                                        <!--begin::Item-->
                                        <div class="navi-item mr-2">
                                            <a href="custom/apps/todo/docs.html" class="navi-link">
                                                <span class="navi-text">Unselected &nbsp;<span class="label label-rounded label-light-success font-weight-bolder" id="uns"><?php echo count((array)$list_data); ?></span></span>
                                            </a>
                                        </div>
                                        <!--end::Item-->
                                        <!--begin::Item-->
                                        <div class="navi-item mr-2">
                                            <a href="custom/apps/todo/files.html" class="navi-link">
                                                <span class="navi-text">Total &nbsp;<span class="label label-rounded label-light-success font-weight-bolder" id="to"><?php echo count((array)$list_data); ?></span> </span>
                                            </a>
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                </div>
                                <!--end::Navigation-->
                            </div>
                            <!--end::Info-->
                            <!--begin::Users-->
                            <div class="symbol-group symbol-hover py-2">

                                <div class="symbol symbol-30 symbol-light-primary" data-toggle="tooltip" data-placement="top" title="New user" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                    <span class="symbol-label font-weight-bold">
                                        <i class="fa flaticon2-plus font-size-sm"></i>
                                    </span>
                                </div>
                            </div>
                            <!--end::Users-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Head-->
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-xl-6">
                            <!--begin::Card-->
                            <div class="card card-custom card-stretch" id="kt_todo_list">
                                <!--begin::Header-->
                                <div class="card-header align-items-center flex-wrap py-6 border-0 h-auto">
                                    <!--begin::Toolbar-->
                                    <div class="d-flex flex-wrap align-items-center">
                                        <div class="d-flex align-items-center mr-1 my-2">
                                            <label data-inbox="group-select" class="checkbox checkbox-inline checkbox-primary mr-3">
                                                <input aria-label="Checkall" type="checkbox" name="checkall" onclick="checkall()" id="checkall" value="0" onkeydown="">
                                                <span class="symbol-label"></span>
                                            </label>
                                            <?php $lists_status = toGetStatusProgressAction($transaction_type, $status);?>
                                        </div>
                                        <div class="d-flex align-items-center mr-1 my-2">

                                                <?php foreach($lists_status as $list_status){?>
                                                 <button class="btn btn-light-primary font-weight-bold mr-2" onclick="submitstatus(<?php echo "'".$transaction_type."'"; ?> , <?php echo "'".$list_status['status']."'"; ?>)" name="status" id="status" value="<?php echo $list_status['status']; ?>"><?php echo $list_status['action_label']; ?></button>
                                                <?php } ?>

                                            </div>


                                    </div>
                                    <!--end::Toolbar-->
                                    <!--begin::Actions-->

                                    <!--end::Actions-->
                                </div>
                                <!--end::Header-->
                                <!--begin::Body-->
                                <div class="card-body p-0">
                                    <!--begin::Responsive container-->
                                    <div class="table-responsive">
                                        <!--begin::Items-->
                                        <div class="list list-hover min-w-500px" data-inbox="list">
                                            <!--begin::Item-->
                                            <?php $j=1; foreach ($list_data as $list){ ?>
                                            <div class="d-flex align-items-start list-item card-spacer-x py-4" data-inbox="message">
                                                <!--begin::Toolbar-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Actions-->
                                                    <div class="d-flex align-items-center mr-3" data-inbox="actions">
                                                        <!--begin::Checkbox-->
                                                        <label class="checkbox checkbox-inline checkbox-primary flex-shrink-0 mr-3">
                                                            <input type="hidden" id="checked_<?php echo $j; ?>" value="0">
                                                        <input aria-label="Checkbox" type="checkbox" onclick="singlecheck(this.id,<?php echo $j; ?>)" id="check_<?php echo $j; ?>"  value="<?php echo  $list['transaction_id']; ?>" onkeydown="">
                                                            <span></span>
                                                        </label>


                                                        <!--end::Checkbox-->


                                                    </div>
                                                    <!--end::Actions-->
                                                </div>
                                                <!--end::Toolbar-->
                                                <!--begin::Info-->
                                                <div class="flex-grow-1 mt-1 mr-2" data-toggle="view"   onclick="ajaxview(<?php echo $list['transaction_id'];  ?>,<?php echo"'".$list['transaction_type']."'";  ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                    <!--begin::Title-->
                                                    <div class="font-weight-bolder mr-2"><?php echo $list['party_name']." - ".$list['transaction_number'] ;?></div>
                                                    <!--end::Title-->
                                                    <!--begin::Labels-->

                                                    <div class="mt-2">
                                                        <span class="label label-light-primary font-weight-bold label-inline"><?php echo date('M d, Y',strtotime($list['transaction_date'])); ?></span>
                                                        <span class="label label-light-primary font-weight-bold label-inline"><?php echo $gen_code[$list['type']]; ?></span>
                                                        <?php $opex_cpex = $list['transaction_number'] == "1" ? : 'Cpex' ; 'Opex';?>
                                                        <span class="label label-light-primary font-weight-bold label-inline"><?php echo $opex_cpex; ?></span>
                                                        <span class="label label-light-primary font-weight-bold label-inline"><?php echo customeummarmonyformate($list['total_amount']); ?></span>
                                                    </div>
                                                    <!--end::Labels-->
                                                </div>
                                                <!--end::Info-->
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center justify-content-end flex-wrap" data-toggle="view">
                                                    <!--begin::Datetime-->
                                                    <div class="font-weight-bolder" data-toggle="view">
                                                        <?php $days = dateDiffInDays($list['request_date']);
                                                    if($days == 0){ echo "To day";
}
                                                    elseif($days < 7){ echo $days ." days ago";
}
                                                    else{ echo date('M d, Y',strtotime($list['request_date']));
}
                                                 ?>
                                                    </div>
                                                    <!--end::Datetime-->
                                                    <!--begin::User Photo-->
                                                    <span class="symbol symbol-30 ml-3">
                                                        <?php if($users[$list['originator']]['photo'] != ''){ ?>
                                                        <span class="symbol-label" style="background-image: url('uploads/all/<?php echo $users[$list['originator']]['photo']; ?>')"></span>
                                                        <?php }else{ ?>
                                                            <div class="symbol symbol-light-danger symbol-30 ml-3">
                                                                <span class="symbol-label font-weight-bolder"><?php echo strtoupper(substr($users[$list['originator']]['name'],0,1));?></span>
                                                            </div>

                                                        <?php } ?>
                                                    </span>
                                                    <!--end::User Photo-->
                                                </div>
                                                <!--end::Details-->
                                            </div>
                                            <!--end::Item-->
                                            <?php $j++;
} ?>
                                            <input type="hidden" name="row" id="row" value="<?php echo $j; ?>">
                                        </div>
                                        <!--end::Items-->
                                    </div>
                                    <!--end::Responsive container-->
                                    <!--begin::Pagination-->

                                    <!--end::Pagination-->
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <div class="col-xl-6 pt-10 pt-xl-0">
                            <!--begin::Card-->
                            <div class="card card-custom card-stretch" id="kt_todo_view" >
                                <div id="ajaxdisplay">

                                </div>

                            </div>
                            <!--end::Card-->
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
            </div>

        </div>
        <!--end::Todo-->
    </div>
    <!--end::Container-->
</div>
