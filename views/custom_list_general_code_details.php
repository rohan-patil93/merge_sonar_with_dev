<?php
$approval_url = API."?mode=togetgeneralcodedetails&work_module_setup_id=".$_SESSION['work_module_setup_id']."&genc_code=".$_GET['CODE'];
$approval_data = callAPI($approval_url);
$gendata = json_decode($approval_data,1);
$code = $gendata['codes'];
$data = $gendata['details'];
$roles = $gendata['roles'];
$role_permission = $gendata['role_permission'];
$role_admin = $gendata['role_admin'];
if(isset($_GET['CODE']) && $_GET['CODE']!=""){
    $view_code = $_GET['CODE'];
}else{
    $view_code = 'ALIGNMENT';
}
$working_role_short_code = $_SESSION['working_role_short_code'];
?>
<script src="assets/js/custom_stationery_available_items.js"></script>
<script>
    function creategeneralcode(){
        jQuery.ajax({
            url: "views/custom_insert_general_code_popup.php",
            data:'option=noption',
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            },
            error:function (){}
        });

    }
    function getgencDetails(log_id){
        var htmloader = '<div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#getlogdetailshow').html(htmloader);
        if(log_id == ""){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "<?php echo $_SESSION['error_message'][1013];?>"
            })
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
    function addnewrowforgen(val){
        var htmlRows = '';
        htmlRows += '<div class="col-lg-3  p-1"><input aria-label="Genc Code[]" type="text" readonly class="form-control" name="genc_code[]" value="'+val+'"></div>';
        htmlRows += '<div class="col-lg-4 p-1"><input aria-label="Genc Detail Code[]" type="text" requried class="form-control" name="genc_detail_code[]" value=""></div>';
        htmlRows += '<div class="col-lg-4 p-1"><input aria-label="Genc Detail Name[]" type="text" requried class="form-control" name="genc_detail_name[]" value="<?php echo $detail['genc_detail_name'] ?>"></div>';
        htmlRows += '<div class="col-lg-1 p-1 mt-10"><button type="button" onclick="savegenralcodedetails()"></button> </div>';
        $('#getgencdetailsone').append(htmlRows);
    }
    function savegenralcodedetails(){
        var formvalue =  $("#save-genccode-details").serialize();
        console.log(formvalue);
        jQuery.ajax({
            url: "ajax.php",
            data: "page=savegenralcodedetails&"+formvalue,
            type: "POST",
            success:function(data){
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001'];?>','');
                window.location.reload();
            }
        });
    }
    function gnrolepermision(code, role) {
        jQuery.ajax({
            url: "ajax.php",
            data: "page=gnrolepermision&code="+code+"&role="+role,
            type: "POST",
            success:function(data){
                console.log(data);
                if(data=='Y'){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',role+' Assigned','');
                }
                if(data=='N'){
                    tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',role+' Unassigned','');
                }
            }
        });
    }
    function deletegnccodes(code,id){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=setupcomponents&table_name=general_code_details&is_active=1&id='+id,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#new_"+id).remove();
            },
            error:function (){}
        });
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card">
            <?php if(count((array)$code)>0){
                if($working_role_short_code=='ADMIN'|| in_array($working_role_short_code,(array)$role_permission)){
            ?>
            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header">
                    <h5>General Code Details</h5>
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

                                    <?php $menuid = 1; foreach ($code as $cod) {
                                        $trnsatable = $display_attributes[$tablename] !=""?$display_attributes[$tablename]:$tablename;
                                        ?>
                                        <div class="p-10 z-depth-top-0" id="topcat_<?php echo $menuid; ?>" <?php if($view_code == $cod['genc_code'] ){ ?> style="background-color: #4099ff"; <?php
} ?> >
                                            <input type="hidden" id="showtype_<?php echo $menuid; ?>" value="N">
                                            <button type="button" class="btn btn-link text-start text-decoration-none d-block w-100 p-0 border-0" <?php if($view_code == $cod['genc_code'] ){ ?> style="color: #FFFFFF"; <?php
} ?> onclick="getgencDetails(<?php echo $cod['genc_code']; ?>)"><?php echo viewTitle($cod['genc_name']); ?>
                                            </button>
                                    </div>

                                    <?php $menuid++;
} ?>
                                </div>
                            </div>
                            <div class="col-lg-7" >
                                <div class="card-header" style="padding-left: 0px;">
                                    <h5>General Code - ( <?php  echo $view_code;  ?> )</h5>
                                </div>
                                <form class="form wizard-form" id="save-genccode-details" method="post" action="" enctype="multipart/form-data">
                                <div class="col-lg-12 row p-2" id="getgencdetailsone">

                                    <div class="col-lg-3 p-1"> Categories </div>
                                    <div class="col-lg-4 p-1">General  Code</div>
                                    <div class="col-lg-4 p-1">Lable</div>
                                    <div class="col-lg-1 p-1"></div>

                                    <input type="hidden" name="code" value="<?php echo $view_code; ?>">
                                    <?php $m=1; foreach((array)$data[$view_code] as $detail){ ?>

                                    <div class="col-lg-12 row" id="new_<?php echo $detail['id']; ?>">
                                        <div class="col-lg-3  p-1">
                                            <input aria-label="Genc Code[]" type="text" readonly class="form-control" name="genc_code[]" value="<?php echo $detail['genc_code'] ?>">

                                        </div>
                                        <div class="col-lg-4 p-1">
                                            <input aria-label="Genc Detail Code[]" type="text" class="form-control" name="genc_detail_code[]" value="<?php echo $detail['genc_detail_code'] ?>">
                                        </div>
                                        <div class="col-lg-4 p-1">
                                            <input aria-label="Genc Detail Name[]" type="text" class="form-control" name="genc_detail_name[]" value="<?php echo $detail['genc_detail_name'] ?>">
                                        </div>
                                        <div class="col-lg-1 p-1 m-t-10">
                                            <button type="button" class="btn btn-link p-0 border-0" onclick="deletegnccodes('<?php echo $view_code; ?>',<?php echo $detail['id']; ?>)">
                                                <i class="ti-trash text-danger f-20"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php $m++;
}  ?>

                                </div>
                                <div class="col-lg-12 p-1">
                                    <div class="col-lg-2">
                                        <button type="button" onclick="addnewrowforgen('<?php echo $view_code; ?>')" class="btn btn-success">+ ADD</button>
                                    </div>
                                </div>
                                <div class="col-lg-12 p-1 mt-3 f-15">
                                    <span class="text-warning f-w-600"> Note :</span>
                                    This records must be unique. Duplicate values are not permitted.
                                </div>
                                <div class="col-lg-11 p-1 text-right">
                                    <button type="button" onclick="savegenralcodedetails()" class="btn btn-primary">Save</button>
                                </div>
                                </form>
                            </div>
                            <?php if($working_role_short_code=='ADMIN'|| $working_role_short_code==$role_admin){ ?>
                            <div class="col-lg-3">
                                <div class="card-header" style="padding-left: 0px;">
                                    <h5>Assign Roles</h5>
                                </div>
                                <!-------------Role Start------------->
                                <div class="border-checkbox-section p-2">
                                    <?php foreach($roles as $role){ ?>
                                    <div class=" col-lg-12 border-checkbox-group border-checkbox-group-primary">
                                    <input aria-label="Input field" class="border-checkbox" <?php if(in_array($role['role_short_code'],(array)$role_permission)){ echo "checked";
} ?> onclick="gnrolepermision('<?php echo $view_code; ?>','<?php echo $role['role_short_code']; ?>')" type="checkbox" id="role<?php echo $role['id']; ?>" onkeydown="">
                                    <label class="border-checkbox-label" for="role<?php echo $role['id']; ?>"><?php echo $role['display_name']; ?></label>
                                    </div>
                                    <?php } ?>
                                </div>
                                <!-----------Role End-------------->
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php }else{
                    include_once 'views/403.php';
             }
            }else{
                include_once 'views/page_error.php';
             } ?>
        </div>
    </div>
</div>

