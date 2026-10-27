<?php
if (isset($_GET['PG'])) {
    $pageno = $_GET['PG'];
} else {
    $pageno = 1;
}
$company_id =$_SESSION['company_id'];
$emp_id = $_SESSION['emp_id'];
$search_note ="";
if(isset($_GET['FIND']) && $_GET['FIND'] !='') {
    $search_value = $_GET['FIND'];
    $search_value = urldecode($search_value);
}
include_once "new_from_submit.php";
$offset = ($pageno-1) * NO_OF_RECORDS_PER_PAGE;
$vurl = API."?mode=togetnoteslist&company_id=".$company_id."&employee_id=".$emp_id."&is_active=0&offset_limit=".$offset."&no_of_records_per_page=".NO_OF_RECORDS_PER_PAGE."&selected_notes=".$search_value;
$configesponse = callAPI($vurl);
$note_data_list = json_decode($configesponse,1);
$note_count = $note_data_list['total_notes'];
$notes_list = $note_data_list['notes_list'];
$share_with = $note_data_list['share_with'];
$share_u = $note_data_list['share_u'];
$count = count((array)$share_with);
$number_of_pages =  ceil($note_count / NO_OF_RECORDS_PER_PAGE);

?>
<script type="text/javascript">
    function displaycontent(data){
        jQuery.ajax({
            url: "views/custom_dynamic_content_display_topup.php",
            data:"header=Content&data="+data,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            },
            error:function (){}
        });

    }
    function dynamiceditpopup(option,id){
        jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option+"&Rid="+id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            },
            error:function (){}
        });
    }

    function dynamicinsertpopup(option){
jQuery.ajax({
    url: "views/new_from.php",
    data:'option='+option,
    type: "POST",
    success:function(data){
        $('#dynamic_popup1').html(data);
        $('#kt_modal_dynamic_popup').modal("show");
    },
    error:function (){}
});
}
function shareprofile(id){
    jQuery.ajax({
        url: "views/custom_insert_share_details.php",
        data:'share_table_id='+id+'&share_table=employee_notes',
        type: "POST",
        success:function(data){
            $('#dynamic_popup1').html(data);
            $('#kt_modal_dynamic_popup').modal("show");
        },
        error:function (){}
    });
}
</script>
<div class="flex-column-fluid ">
    <div class="container-fluid j-pro">
        <div class=" flex-row j-content">
            <div class="card card-custom ">
                <div class="card-header">
                    <h5>Note</h5><span><?php echo $note_count;?> Total</span>
                    <div class="card-header-right">
                    <button type="button" onclick="dynamicinsertpopup('COM_INSERT_EMPLOYEE_NOTES')" class="btn btn-success ">
                                              <i class="icofont icofont-plus icon-white"></i>ADD NOTES  </button>
                          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                              <i class="icofont icofont-reply"></i>
                              Back
                            </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(count((array)$share_u)>0 || count((array)$notes_list)>0){ ?>
                    <div class="row">
                        <div class="row">
                            <?php foreach ($notes_list as $notesdata) {
                                $display_name =$_SESSION['users'][$notesdata['created_by']];
                                $created_by_image =$_SESSION['user_image'][$notesdata['created_by']];
                                ?>
                                <div class="col-xl-4 col-md-6">
                                    <div class="card new-cust-card">
                                        <div class="card-header">
                                            <button type="button" onclick="shareprofile(<?php echo $notesdata['id']; ?>)"  class="btn btn-default waves-effect my-note-icon" ><i class="fa fa-share-alt"></i></button>
                                            <div class="card-header-right">
                                                <button type="button" onclick="dynamiceditpopup('COM_INSERT_EMPLOYEE_NOTES',<?php echo "'".deocdeId("id:".$notesdata['id'])."'" ?>)"  class="btn btn-default waves-effect my-note-icon" ><i class="fa fa-edit"></i></button>
                                            </div>
                                        </div>
                                        <div class="px-3">
                                            <div class="align-middle m-b-25">
                                                <?php if($created_by_image!=""){ ?>
                                                    <img src="uploads/all/<?php echo $created_by_image; ?>"   class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                                <?php } else { ?>
                                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($display_name); ?></span>
                                                <?php } ?>
                                                <div class="d-inline-block">
                                                    <a href="#!"><h6 class="f-w-600"><?php echo $notesdata['notes_title']; ?></h6></a>

                                                </div>
                                                <p class="text-muted m-t-10">
                                                    <?php if(strlen($notesdata['notes'])>300){
                                                            echo substr($notesdata['notes'],0,150)."..."; ?>
                                                        <button type="button" onclick="displaycontent(<?php echo "'".$notesdata['notes']."'"; ?>)" class="text-primary cursor-pointer">More </button>
                                                    <?php    }else{
                                                            echo $notesdata['notes'];
                                                        } ?>
                                                </p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="card-footer">
                                            <div class="d-flex">
                                                <div class="symbol-group symbol-hover">
                                                    <div class="symbol-group symbol-hove ml-2r">
                                                    </div>
                                                </div>

                                                <?php
                                                $share = $notesdata['share_with_details'];
                                                foreach($share as $shaerlist){
                                                    $i = 0; if($i==5){ break;
} $i++; ?>
                                                    <?php if($shaerlist['shared_image']!=""){ ?>
                                                        <img src="uploads/all/<?php echo $shaerlist['shared_image']; ?>" data-bs-toggle="tooltip" data-bs-original-title="<?php echo $shaerlist['shared_display_name']; ?>"  class="img-radius img-30" alt="User">
                                                    <?php } else { ?>
                                                        <div class="symbol symbol-35 symbol-circle" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $shaerlist['shared_display_name']; ?>">
                                                            <span class="symbol-label font-weight-bold"> <?php echo initials($shaerlist['shared_display_name']); ?></span>
                                                        </div>
                                                    <?php } ?>
                                                    <?php } ?>
                                                    <?php $show = $count-5; if($show>0) {  ?>
                                                        <div class="symbol symbol-35 symbol-circle" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="">
                                                            <span class="symbol-label font-weight-bold"> <?php
                                                            echo $show."+";?> </span>
                                                        </div>

                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php  }    ?>
                            </div>
                        </div>
                        <?php if(count((array)$share_u)>0){ ?>
                            <div class="row">
                                <div class="card-header m-b-10">
                                <h5>Shared Notes</h5>
                                </div>
                            <?php foreach ($share_u as $notesdata) {
                                $display_name =$_SESSION['users'][$notesdata['created_by']];
                                $created_by_image =$_SESSION['user_image'][$notesdata['created_by']];
                                ?>
                                <div class="col-xl-4 col-md-6">
                                    <div class="card new-cust-card">
                                        <div class="card-header">
                                            <div class="card-header-right">

                                            </div>
                                        </div>
                                        <div class="card-block">
                                            <div class="align-middle m-b-25">
                                                <?php if($created_by_image!=""){ ?>
                                                    <img src="uploads/all/<?php echo $created_by_image; ?>"   class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                                <?php } else { ?>
                                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($display_name); ?></span>
                                                <?php } ?>
                                                <div class="d-inline-block">
                                                    <a href="#!"><h6 class="f-w-600"><?php echo $notesdata['notes_title']; ?></h6></a>

                                                </div>
                                                <p class="text-muted m-t-10">
                                                    <?php if(strlen($notesdata['notes'])>300){
                                                            echo substr($notesdata['notes'],0,150)."..."; ?>
                                                        <button type="button" onclick="displaycontent(<?php echo "'".$notesdata['notes']."'"; ?>)" class="text-primary cursor-pointer">More </button>
                                                    <?php    }else{
                                                            echo $notesdata['notes'];
                                                        } ?>
                                                </p>
                                            </div>
                                        </div>
                                         </div>
                                    </div>
                                <?php  } ?>
                            </div>
                        <?php } ?>
                    <?php }else{ ?>
                        <div class="card-px text-center ">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Notes</p>
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

