<?php
$url = API."?mode=getperformanaceappraiserheader&assessment_year=".$assemnt_year."&appraisal_id=".$performance_appraisal_setup_id;
$response = callAPI($url);
$kipandkradetails = json_decode($response,1);
$paheaders  = $kipandkradetails['performance_appraisal_headers'];
$performance_setup  = $kipandkradetails['performance_setup'];
if(isset($_POST['performanace_appraiser_header_sbt'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=inserperformanaceappraiserheader&method=post";
    $response0 = file_post_contents($url, $_POST, 'insert');
    $response1 = json_decode($response0,1);
    $response = $response1['exicute'];
    if($response['status'] == 'fail'){
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }else{ ?>
            <script type="text/javascript">
                document.onreadystatechange = function(){
                    if(document.readyState == "complete") {
                         tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                    }
                }
            </script>
        <?php
    }
}

?>
<script type="text/javascript">
    function subHeaderTableAdded(id){
        var rowCount = $('#dinamictable_'+id+' th').length;
        jQuery.ajax({
            url: "views/custom_insert_performance_appraisal_headers_ajax.php",
            data:'new_row=Y&rowCount='+rowCount+'&rid='+id,
            type: "POST",
            success:function(htmlRows){
                $('#subheadertable').append(htmlRows);

                rowCount2 = rowCount-2;
                console.log(rowCount);
                $("#dinamictable_"+id).find('th').eq(rowCount2).after('<th scope="col" class="head_text_'+id+rowCount+'">Column</th>');
                $("#dinamictable_"+id).find('td').eq(rowCount2).after('<td><div class="" id="chengetypetextselect_'+id+rowCount+'"><input aria-label="Input field" type="text" class="form-control" id="" value=""></div></td>');
            }
        });
    }
    function addMoreHeader(){
            var headerformname = $("#headerformname1").serialize();
            jQuery.ajax({
                url: "ajax.php",
                data:'page=inserperformanaceappraiserheader&'+headerformname,
                type: "POST",
                success:function(htmlRows){
                    console.log(htmlRows);
                var htmlRow = JSON.parse(htmlRows);
                console.log(htmlRow);
                $('.appendheader').html(htmlRow.INSERT_PAGE);
                $('.appendnewaccordion').append(htmlRow.VIEW_HTML_PAGE);

                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                var elem = Array.prototype.slice.call(document.querySelectorAll('.js-multidynamic'+htmlRow.LASTID));
                elem.forEach(function(html) {
                    var switchery = new Switchery(html, { color: '#4099ff', jackColor: '#fff',size: 'medium' });
                });
                }
            });
    }
    function performanaceAppraiserHeaderSbt(){
        var headerformname = $("#headerformname1").serialize();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=updateperformanaceappraiserheader&'+headerformname,
            type: "POST",
            success:function(htmlRows){
                var respon = JSON.parse(htmlRows);
                console.log(respon);
                console.log(respon.EXICUTION_STATUS['exicute']);
                var errorresult = dynamicErrorShow(respon.EXICUTION_STATUS['exicute']);
                if(errorresult.result == '0'){  // 0 successfull result
                     tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                }
            }
        });
    }
    function editHeader(id){
        jQuery.ajax({
            url: "views/custom_insert_performance_appraisal_headers_ajax.php",
            data:'rid='+id,
            type: "POST",
            success:function(htmlRows){
                console.log(htmlRows);
            var htmlRow = JSON.parse(htmlRows);
            console.log(htmlRow);
            $('.appendheader').html(htmlRow.INSERT_PAGE);
            $(".js-example-basic-single").select2();
            $(".js-example-disabled-results").select2();
            var elem = Array.prototype.slice.call(document.querySelectorAll('.js-multidynamic'+htmlRow.LASTID));
            elem.forEach(function(html) {
                var switchery = new Switchery(html, { color: '#4099ff', jackColor: '#fff',size: 'medium' });
            });
            }
        });
    }
    function changetype(id){
        var type = $("#type_"+id).val();
        var textoption = "";
        if(type == 'S'){
            textoption ='<select aria-label="Select option" class="form-select js-example-basic-single"><option>Yes</option><option>No</option><option>maybe</option></selecte>';

        }else{
            textoption ='<input aria-label="Input field" type="text" class="form-control" id="" value="">';
        }
        $("#chengetypetextselect_"+id).html(textoption)
        $(".js-example-basic-single").select2();
    }
    function dynamicPage(id){
        if($("#dynamic_page_check").is(":checked")){
            $(".pagenameshow").show('slow');
            $(".dynamic_page_Y_"+id).show('slow');
            $(".dynamic_page_N_"+id).hide('slow');
            $(".dynamicpageshow").hide('slow');

        }else{
            $(".pagenameshow").hide('slow');
            $(".dynamic_page_Y_"+id).hide('slow');
            $(".dynamic_page_N_"+id).show('slow');
            $(".dynamicpageshow").show('slow');
        }
    }

    function addMoreButtonSetting(id){
        if($("#sub_header_addmore_check").is(":checked")){
            $(".addemorebutton_"+id).show('slow');
    }else{
            $(".addemorebutton_"+id).hide('slow');
        }
    }

    function deletepahrow(id){
        Swal.fire({
          title: 'Do you want to Delete the Field?',
          showDenyButton: true,
          showCancelButton: false,
          confirmButtonText: 'Yes',
          denyButtonText: `No`,
        }).then((result) => {
          if (result.isConfirmed) {
                $('.'+id).remove();
          }
        })
    }

</script>
<div class="flex-column-fluid ">
    <div class="">
        <div class="row j-content">  <!-- j-content -->
            <div class="col-lg-12">
                <?php
 if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header">
                        <h5>Performance Appraisal Headers</h5>
                        <div class="card-header-right">
                            <?php if($_GET['option'] != "COM_VIEW_PERFORMANCE_APPRAISAL_SETTINGS"){ ?>
                     <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                  <i class="icofont icofont-reply"></i>  Back
                     </a>
                    <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <form method="post" id="headerformname1">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group mb-2"  >
                                                <span class="form-label">Appraisal Name</span>
                                                <input type="hidden" class="form-control" name="appraisal_id"  value="<?php echo $performance_appraisal_setup_id ?>">
                                                <input aria-label="Input field" type="text"  class="form-control"  value="<?php echo $performance_setup['name']; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group mb-2"  >
                                                <span class="form-label">Assessment Year</span>
                                                <input aria-label="Assessment Year" type="text" class="form-control"  name="assessment_year" value="<?php echo $assessment_year ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-12"><span class="text-danger f-14 error_messgae_apriid_year"></span></div>
                                        <button type="button" class="btn btn-sm font-weight-bolder btn-light-primary btn-primary col-lg-6 mt-2 mb-2" onclick="addMoreHeader()"><i class="fa fa-plus"></i>Add More Header</button>
                                    </div>
                                    <div class="appendheader custom-scroll" style=" height: 600px;">
                                    </div>

                                </form>
                            </div>
                            <div class="col-lg-8 custom-scroll" style=" height: 800px;">
                <div class="accordion-block color-accordion-block">
                    <div class="color-accordion appendnewaccordion"  >


                        <?php foreach($paheaders as $paheaders){
                        $loginuser_type = "EMP";
                            $header_display_setting = json_decode($paheaders['header_display_setting'],1);
                            $sub_header_name = json_decode($paheaders['sub_header_name'],1);
                            $paheadersrow = $paheaders['id'];
                            ?>
                            <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2  bg-primary" >
                                <div class="row">
                                    <div class="col-lg-11">
                                        <span id="header_name_<?php echo $paheaders['id']; ?>"><?php echo $paheaders['header_name']; ?></span>
                                        <p class="f-w-400" id="header_comment_<?php echo $paheaders['id']; ?>"><?php echo $paheaders['header_comment']; ?></p>
                                    </div>
                                    <div class="col-lg-1">
                                        <i class="fa fa-edit" onclick="editHeader('<?php echo $paheaders['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                                    </div>
                                </div>
                            </a>
                            <div class="accordion-desc">
                                <p class="dynamic_page_Y_<?php echo $paheaders['id']; ?>" <?php if($paheaders['dynamic_page']=='0'){
}else{ echo "style='display:none'";
} ?>>Page URL</p>
                                <div class="table-responsive dynamic_page_N_<?php echo $paheaders['id']; ?>" <?php if($paheaders['dynamic_page']=='1'){
}else{ echo "style='display:none'";
} ?>>
                                        <table class="table table-bordered" id="dinamictable_<?php echo $paheaders['id']; ?>">
                                            <thead>
                                                <tr>
                                                    <?php $fildcnt = 1; foreach($sub_header_name as $sub_headerres){
                                                        $idd = "deletrow_".$paheaders['id'].$fildcnt;
                                                        ?>
                                                        <th scope="col" class="head_text_<?php echo $paheaders['id'].$fildcnt; ?> <?php echo $idd;  ?>"><?php echo $sub_headerres['field_name']; ?></th>
                                                    <?php $fildcnt++;
}  ?>

                                                        <th scope="col" class="addemorebutton_<?php echo $paheaders['id']; ?>" <?php if($paheaders['sub_header_addmore']=='0'){
}else{ echo "style='display:none'";
} ?>>Action</th>

                                                </tr>
                                            </thead>
                                            <tbody data-repeater-list="">
                                                <?php $hedcnt[$paheadersrow] = 1;
                                                for($hed = 0;$hed < 1;$hed++){

                                                 ?>
                                                    <tr id="deleterow_<?php echo $paheaders['id'].$hedcnt[$paheadersrow]; ?>" class="">
                                                        <input type="hidden" class="form-control" name="row_id[<?php echo $paheaders['id']; ?>][]" value="">

                                                        <?php $fieldcnt = 1; foreach($sub_header_name as $sub_headerres){
                                                        $idd = "deletrow_".$paheaders['id'].$fieldcnt;
                                                            $texttype = $sub_headerres["type"] =="T"?"selected":"";
                                                            $selecttype = $sub_headerres["type"] =="S"?"selected":"";
                                                         ?>
                                                            <td class="<?php echo $idd; ?>">
                                                                <div  id="chengetypetextselect_<?php echo $paheaders['id'].$fieldcnt; ?>">
                                                                    <?PHP if($sub_headerres["type"] =="S"){ ?>
                                                                        <select aria-label="Select option" class="form-select js-example-basic-single"><option>Yes</option><option>No</option><option>maybe</option></selecte>
                                                                    <?php }else{ ?>
                                                                        <input aria-label="Input field" type="text" class="form-control" id="" value="">
                                                                    <?php } ?>
                                                                </div>
                                                            </td>
                                                        <?php  $fieldcnt++;
} ?>

                                                            <td class="addemorebutton_<?php echo $paheaders['id']; ?>" <?php if($paheaders['sub_header_addmore']=='0'){
}else{ echo "style='display:none'";
} ?>>
                                                                <button type="button" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger btn-primary" >
                                                                    <i class="fa fa-trash"></i>Delete
                                                                </button>
                                                            </td>

                                                    </tr>
                                                    <?php $hedcnt[$paheadersrow]++;
} ?>
                                                </tbody>
                                            </table>

                                                <button type="button" class="btn btn-sm font-weight-bolder btn-light-primary btn-primary addemorebutton_<?php echo $paheaders['id']; ?>" <?php if($paheaders['sub_header_addmore']=='0'){
}else{ echo "style='display:none'";
} ?>>
                                                    <i class="fa fa-plus"></i>Add
                                                </button>

                                        </div>
                                </div>
                            <?php  } ?>
                        </div>
                    </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
