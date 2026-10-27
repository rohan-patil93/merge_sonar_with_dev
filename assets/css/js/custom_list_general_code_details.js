window.addEventListener("load", function() {
$(document).ready(function() {
    $('#add_genral_codes').on('click', function() {
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
    });
    $('#getgencDetails').on('click', function() {
        //log_id
        var htmloader = '<div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
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
    });
    $('#addnewrowforgen').on('click', function() {
        var htmlRows = '';
        //val
        htmlRows += '<div class="col-lg-3  p-1"><input type="text" readonly class="form-control" name="genc_code[]" value="'+val+'"></div>';
        htmlRows += '<div class="col-lg-4 p-1"><input type="text" requried class="form-control" name="genc_detail_code[]" value=""></div>';
        htmlRows += '<div class="col-lg-4 p-1"><input type="text" requried class="form-control" name="genc_detail_name[]" value="<?php echo $detail['genc_detail_name'] ?>"></div>';
        htmlRows += '<div class="col-lg-1 p-1 mt-10"><a href="#" onclick="savegenralcodedetails()"></a> </div>';
        $('#getgencdetailsone').append(htmlRows);
    });
    $('#savegenralcodedetails').on('click', function() {
        var formvalue =  $("#save-genccode-details").serialize();
        console.log(formvalue);
        jQuery.ajax({
            url: "ajax.php",
            data: "page=savegenralcodedetails&"+formvalue,
            type: "POST",
            success:function(data){
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001'];?>','');
                window.location.href=window.location.href;
            }
        });
    });
    $('#gnrolepermision').on('click', function() {
        //code, role
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
    });
    $('#deletegnccodes').on('click', function() {
        jQuery.ajax({
            url: "ajax.php",
            data:'page=setupcomponents&table_name=general_code_details&is_active=1&id='+id,
            type: "POST",
            success:function(data){
                console.log(data);
                //window.location.href = 'index.php?option=COM_LIST_GENERAL_CODE_DETAILS&CODE='+code;
                $("#new_"+id).remove();
            },
            error:function (){}
        });
    });
});
});