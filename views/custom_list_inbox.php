<?php
if($inbox_already_call !='Y'){
    $approval_url = API."?mode=togettransactionLoginbox&status=".$_GET['status'];
    $approval_data = callAPI($approval_url);
    $approval_data = json_decode($approval_data,1);
}
$category = $approval_data['transactiores'];
$employee = $approval_data['emp'];
$alltransactiores = $approval_data['alltransactiores'];
$transactionlog = $approval_data['transactionlog'];
$notificationresult = $approval_data['notificationresult'];
$stausresult = ViewStatusData();
$cntinboc = $approval_data['cnt_inbox'];
$display_attributes = $approval_data['display_attributes'];
?>
<script>
    function showSubMenu(id){
        var showtype = $("#showtype_"+id).val();
        if(showtype == 'N'){
            $("#showsubmienu_"+id).show("slow");
            $("#showtype_"+id).val("Y");
            $("#topcat_"+id).addClass('current');
        }else{
            $("#showsubmienu_"+id).hide("slow");
            $("#showtype_"+id).val("N");
            $("#topcat_"+id).removeClass('current');
        }
    }
    function loadPage(option,id){
        var htmloader = '<div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#showdivpagehtml').html(htmloader);
        if(option == ""){
            // Swal.fire({
            //     icon: 'error',
            //     title: 'Oops...',
            swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "<?php echo $_SESSION['error_message'][1013];?>", "error");
            $("#tablshrink").removeClass('col-lg-3');
            $("#tablshrink").addClass('col-lg-12',100);
            $('#showdivpagehtml').html(' ');
            return false;
        }
        $("#tablshrink").removeClass('col-lg-12');
        $("#tablshrink").addClass('col-lg-3',100);
        $("#showdivpage").show();
        var closebtnredirect = '<a href="index.php?option='+option+'&Rid='+id+'" class="btn btn-primary m-r-5" onclick="publichappraisal22('+id+')" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Open Page New Tab" target="_blank" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="feather icon-search text-white" ></i>Open New Tab</a><button type="button" !" onclick="closeLoadPage()" class="btn waves-effect waves-light bg-primary" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Close This Page"><i class="ti-close text-white"></i></button>';
        $("#closebtnredirect").html(closebtnredirect);
        jQuery.ajax({
            url: "views/custom_inbox_page_redirection.php",
            data:'option='+option+"&rid="+id,
            type: "POST",
            success:function(htmlRows){
                console.log(htmlRows);
                $('#showdivpagehtml').html(htmlRows);

                //datepicker
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    dateFormat: DateFormate,
                    changeMonth: true,
                    changeYear: true
                });
                //datepicker
                $('[data-bs-toggle="tooltip"]').tooltip();
                $('[data-bs-toggle="popover"]').popover({
                    html: true,
                    content: function() {
                        return $('#primary-popover-content').html();
                    }
                });
            },
            error:function (){}
        });
    }
    function closeLoadPage(){
        $("#tablshrink").removeClass('col-lg-3');
        $("#tablshrink").addClass('col-lg-12',100);
        $("#showdivpage").hide();
        $('#showdivpagehtml').html(' ');
    }
    function getLogDetails(log_id){
        var htmloader = '<div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#getlogdetailshow').html(htmloader);
        if(log_id == ""){
            // Swal.fire({
            //     icon: 'error',
            //     title: 'Oops...',
            swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "<?php echo $_SESSION['error_message'][1013];?>", "error");
            return false;
        }
        jQuery.ajax({
            url: "views/custom_list_inbox_ajax.php",
            data:'log_id='+log_id,
            type: "POST",
            success:function(htmlRows){
                console.log(htmlRows);
                $('#getlogdetailshow').html(htmlRows);
            },
            error:function (){}
        });
    }

</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card">
            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header">
                    <h5>Inbox</h5>
                    <div class="card-header-right">
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="left-menu-sec">
                                    <div class="accordion-msg bg-gray p-2">Inbox   <span class="float-right form-label label label-primary"><?php echo $cntinboc; ?></span></div>
                                    <?php $menuid = 1; foreach ($category as $tablename => $transaction) {
                                            $trnsatable = $display_attributes[$tablename] !=""?$display_attributes[$tablename]:$tablename;
                                        ?>
                                        <div class="accordion-msg main-menu-sec border-0 p-2" id="topcat_<?php echo $menuid; ?>" onclick="showSubMenu('<?php echo $menuid; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                            <input type="hidden" id="showtype_<?php echo $menuid; ?>" value="N">
                                            <a class="d-block" href="javascript:void(0)"><?php echo viewTitle($trnsatable); ?>
                                            <i class="ti-plus float-right mt-1 f-w-600"></i>
                                        </a>
                                    </div>
                                    <div class="p-2 sub-menu-sec" id="showsubmienu_<?php echo $menuid; ?>">
                                        <div class="mt-1 cursor-pointer" onclick="getLogDetails('<?php echo implode(",",(array)$transaction['inpro']); ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">In-Process <span class="float-right form-label label label-primary"><?php echo count((array)$transaction['inpro']);?></span></div>
                                        <div class="mt-1 cursor-pointer" onclick="getLogDetails('<?php echo implode(",",(array)$transaction[ST_APPROVED]); ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved <span class="float-right form-label label label-primary"><?php echo count((array)$transaction[ST_APPROVED]);?></span></div>
                                        <div class="mt-1 cursor-pointer" onclick="getLogDetails('<?php echo implode(",",(array)$transaction[ST_SEND_BACK]); ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Send Back <span class="float-right form-label label label-primary"><?php echo count((array)$transaction[ST_SEND_BACK]);?></span></div>
                                        <div class="mt-1 cursor-pointer" onclick="getLogDetails('<?php echo implode(",",(array)$transaction[ST_REJECTED]); ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Reject  <span class="float-right form-label label label-primary"><?php echo count((array)$transaction[ST_REJECTED]);?></span></div>
                                    </div>
                                    <?php $menuid++;
} ?>
                                </div>
                            </div>
                            <div class="col-lg-10" id="getlogdetailshow">
                                    <?php include_once 'views/custom_list_inbox_ajax.php'; ?>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

