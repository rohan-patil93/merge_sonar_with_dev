<?php

$_SESSION['apply_js']['textediter'] = '1';

if(isset($_POST['submit'])){
    $toc_id = '';
        $data['toc_name'] = $_POST['toc_name'];
        $data['description'] = $_POST['description'];
    if($_POST['toc_id']==""){
        $data['status_date'] = nowDate();
        $data['from_date'] = date("Y-m-d");
        $data['to_date'] = date('Y-m-d', strtotime('+5 years'));
        $data['created_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'];
        $data['company_id'] = $_SESSION['company_id'];
        $url = API."?mode=adddata&table=text_of_contents&method=post";
        $response = file_post_contents($url, $data, 'insert');
        $rest = json_decode($response,1);
        $toc_id = $rest['LASTID'];

        $tocheadrins = array('company_id'=>$_SESSION['company_id'],'text_of_content_id'=>$toc_id,'header_name'=>'Header','header_type'=>'2','created_by'=>$_SESSION['userid']);
        $url2 = API."?mode=adddata&table=toc_headers&method=post";
        $respoo = file_post_contents($url2, $tocheadrins, 'insert');

        $tocheadrins = array('company_id'=>$_SESSION['company_id'],'text_of_content_id'=>$toc_id,'header_name'=>'Header','header_type'=>'3','created_by'=>$_SESSION['userid']);
        $url3 = API."?mode=adddata&table=toc_headers&method=post";
        $respoo = file_post_contents($url3, $tocheadrins, 'insert');

    }else{
        $data['updated_by'] = $_SESSION['userid'];
        $data['updated_date'] = nowDate();
        $url = API."?mode=updaterowtabledata&table=text_of_contents&rid=".$_POST['toc_id']."&method=post";
        $response = file_post_contents($url, $data, 'update');
        $rest = json_decode($response,1);
        $toc_id = $_POST['toc_id'];
   }
     print_r($rest);
   if($rest['status'] != 'fail'){
        $nrid = "id:".$toc_id;
        header("location:index.php?option=CUSTOM_INSERT_TEXT_OF_CONTENTS&Rid=".deocdeId($nrid));
        exit;
   }
}
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(":", $rid);
$toc_id= $rid[1];
$url1 = API."?mode=gettocheader&EID=".$toc_id;
$jsondecode = callAPI($url1);
$result = json_decode($jsondecode,1);
$toc_headers = $result['toc_headers'];
$create_documents_fields = $result['create_documents_fields'];
$text_of_contents = $result['text_of_contents'];
foreach($toc_headers as $tocheaders){
    if($tocheaders['header_type'] == '2'){
            $hearedfooter['header'] = $tocheaders;
        }elseif($tocheaders['header_type'] == '3'){
            $hearedfooter['footer'] = $tocheaders;
        }else{
            $tocheadersparentchield[$tocheaders['parent_toc_header_id']][$tocheaders['id']] = $tocheaders;
        }
    $tocheaders[$tocheaders['id']] = $tocheaders;
}
foreach($create_documents_fields as $result){
    $replacekey = strtolower(str_replace(' ', '_', $result['label']));
    $result['replacefiled'] = "{[".$replacekey."]}";
    $inputFildsArray[] = $result;
}

?>

<script type="text/javascript">
    function addtocheader(){
        CKEDITOR.instances['editor1'].updateElement();
        var postarray = $("#formtocheaders").serialize();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=inserttocheaders&'+postarray,
            success: function(response) {
                console.log(response);
                var respon = JSON.parse(response);
                console.log(respon.res);
                var result = respon.res;
                if(result.status =="fail"){
                   tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',result.message,'');
                }else{
                    if(result.LASTID == '0'){
                       tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Record Updated Successfully','');
                    }else{
                      tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Record Inserted Successfully','');
                    }
                    GetViewReport();

                    editFinanaceGrp(0);
                    console.log('working');

                }
            }
        });
    }
    function editFinanaceGrp(id="",pargrp=""){
        var toc_id = $("#toc_id").val();
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#insertform").html(loader);
        console.log(id);
        console.log(pargrp);
        $.ajax({
            url: "views/custom_insert_toc_headers_form.php",
            type: "POST",
            data: 'id='+id+'&pargrp='+pargrp+'&editform=Y&toc_id='+toc_id,
            success: function(response){
                $("#insertform").html(response);
                $('html, body').animate({
                    scrollTop: $("#insertform").offset().top
                }, 20);
                $(".js-example-basic-single").select2();
                $(".js-example-basic-multiple").select2();
            }
        });
    }
    function GetViewReport(){
        var toc_id = $("#toc_id").val();
        console.log('working -- 25');
        var loader = '<div class="text-center" id="loader-show_2"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#viewtable").html(loader);
        $.ajax({
            url: "views/custom_insert_toc_headers_table.php",
            type: "POST",
            data: 'tableshow=Y&toc_id='+toc_id,
        success: function(response){
                $("#viewtable").html(response);
            }
        });
    }

    function textValueInsert(value,id){
        CKEDITOR.instances[id].insertText(" "+value+" ");
    }

    function viewPreviewTOC(){
        var toc_id = $("#toc_id").val();
       $.ajax({
            url: "views/custom_insert_text_of_contents_popup.php",
            data:'page=1&toc_id='+toc_id,
            type: "POST",
            success:function(data){
                console.log(data);
                $('#popupxl').html(data);
                $('#kt_modal_KTDatatable_remotexl').modal('show');
            },
            error:function (){}
        });
    }
</script>

    <div class="card">
       <div class="card-header">
              <h5>Text Of Content</h5>
              <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
    </div>
        </div>
        <form method="POST">
        <div class="card-body">
            <div class="row">
            <div class="form-group col-lg-4">
                <span class="form-label">Report Name <span class="text-danger">*</span></span>
                <input aria-label="Toc Name" type="text"  class="form-control text-capitalize"  name="toc_name" value="<?php echo $text_of_contents['toc_name']; ?>" />
            </div>
            <div class="form-group col-lg-4">
                <span class="form-label">Description<span class="text-danger">*</span></span>
                <textarea aria-label="Description" type="text"  class="form-control text-capitalize"  name="description"  ><?php echo $text_of_contents['description']; ?></textarea>
            </div>
            <div class="form-group col-lg-2">
                <input type="hidden" name="toc_id" id="toc_id" value="<?php echo $toc_id; ?>">
                <button  type="submit" name="submit" class="btn btn-primary btn-shadow-hover font-weight-bold m-t-30" > <?php echo $toc_id !=""?"Update":"Submit";?> </button>
            </div>
        </div>
        </div>
        </form>
    </div>
       <?php if($toc_id !=""){ ?>
<div class="row">
    <div class="col-lg-3" id="viewtable" >
        <?php include_once 'views/custom_insert_toc_headers_table.php';?>
    </div>
    <div class="col-lg-9" >

        <div class="row">
            <div class="col-lg-9" id="insertform">
                <?php include_once 'views/custom_insert_toc_headers_form.php';?>
            </div>
            <div class="col-lg-3" >
                <div class="card" style="position: sticky;top: 10px;">
                    <div class="card-header">
                        <h5>Field</h5>
                        <div class="card-header-right"></div>
                    </div>
                    <div class="card-body custom-scroll" style="height: 500px;">
                        <ul class="navi navi-hover navi-active">
                        <?php foreach($inputFildsArray  as $key => $value){ ?>
                            <li class="navi-item">
                                <button type="button" class="btn btn-hover-bg-success btn-xs btn-text-success btn-hover-text-white text_added" onclick="textValueInsert('<?php echo $value['replacefiled']; ?>','editor1')">

<?php if($value['field_type'] == '2'){ echo '<i class="fa fa-file-code-o"></i>';
}elseif($value['field_type'] == '3'){ echo '<i class="fa fa-pencil-square-o"></i>';
}else{ echo '<i class="fa fa-mail-reply f-14"></i>';
} ?>
                                 <?php echo ucwords($value['label']); ?>
                                </button>
                            </li>
                        <?php } ?>
                    </ul>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
<?php } ?>
