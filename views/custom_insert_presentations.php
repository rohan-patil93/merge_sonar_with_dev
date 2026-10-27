<?php

$div_count_array = array('0'=>'0','1'=>'12','2'=>'6','3'=>'4','4'=>'3','5'=>'4', '6'=>'4','7'=>'3','8'=>'3','9'=>'4');
$div_count = array('0','1','2','3','4','5', '6','7','8','9');

$vdata1["where"] = array('company_id'=> '1','is_active'=>'0','parent_id'=>'0');
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=presentation&data=".$vdata;

$configesponse = callAPI($vurl);
$data_presentation = json_decode($configesponse,1);

if(isset($_POST['submit_presentation'])){
    $data = array();
    $data['is_active'] = '0';
    $data['current_status'] = 0;
    $data['company_id'] = $_SESSION['company_id'];
    $data['presentation_name'] = $_POST['presentation_name'];
    $data['created_date'] = nowDate();
    $data['created_by'] = $_SESSION['userid'];
    $url = API."?mode=adddata&table=presentation&method=post";
    $response = file_post_contents($url, $data, 'insert');

}
if(isset($_POST['submit_header'])){
    $data = array();
    $data['is_active'] = '0';
    $data['current_status'] = 0;
    $data['company_id'] = $_SESSION['company_id'];
    $data['parent_id'] = $_POST['parent_id'];
    $data['header_name'] = $_POST['header_name'];
    $data['display_sequence'] = $_POST['display_sequence'];
    $data['created_date'] = nowDate();
    $data['created_by'] = $_SESSION['userid'];
    $url = API."?mode=adddata&table=presentation&method=post";
    $response = file_post_contents($url, $data, 'insert');

}
include_once "new_from_submit.php";

if(isset($_GET['PID'] )) {

    $presentation_id = $_GET['PID'];
    $vdata2["where"] = array('company_id'=> '1','is_active'=>'0','parent_id'=>$presentation_id);

    $vdata = urlencode(json_encode($vdata2));
    $vurl = API."?mode=gettabledetailswithconditions&table=presentation&data=".$vdata;

    $configesponse = callAPI($vurl);
    $data_presentation_pages = json_decode($configesponse,1);
    $presentation_name = $data_presentation_pages[0]['presentation_name'];


    if(isset($_GET['PAGEID'])) {
        $presentation_PageId = $_GET['PID'];
        $page_id = $_GET['PAGEID'];

        $vurl = API."?mode=togetpresentationpagedetails&presentation_id=".$presentation_PageId."&page_id=".$page_id;

        $configesponse = callAPI($vurl);
        $presentation_array = json_decode($configesponse,1);
        $page_name = $presentation_array[0]['header_name'];

    }

    if(isset($_GET['DEL'])) {
        $vurl = API."?mode=deletepresentationpagedivision&div_id=".$_GET['DEL'];
        $configesponse = callAPI($vurl);
        $presentation_array = json_decode($configesponse,1);

    }

}

?>

<script type="text/javascript">

    function changePresentation(val) {
        if(val == 'add'){
            $('#popup2').html();
            $('#kt_modal_KTDatatable_remote').modal({ show: true});

        }else{
            window.location.href="index.php?option=CUSTOM_INSERT_PRESENTATIONS&PID="+val;
        }
    }

    function changePresentationPage(val) {
        var pid = document.getElementById("selectPresentation").value;
        alert(pid);
        if(val == 'add'){
            $('#parent_id').val(pid);
            $('#popup3').html();
            $('#kt_modal_KTDatatable_remote2').modal("show");

        }else{
            window.location.href="index.php?option=CUSTOM_INSERT_PRESENTATIONS&PID="+pid+"&PAGEID="+val;
        }
    }
    function dynamicinsertpopup(option){
        var pid = document.getElementById("selectPresentationPage").value;
        jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option+"&display=1&presentation_id="+pid,
            type: "POST",
            success:function(data){
                $('#popup24').html(data);
                $('#kt_modal_KTDatatable_remote1').modal("show");
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
                $('#popup24').html(data);
                $('#kt_modal_KTDatatable_remote1').modal("show");
            },
            error:function (){}
        });
    }

</script>
<!--begin::Content-->
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="alert alert-warning alert-dismissible">
            <strong><?php echo "This function is used for maintaining <strong>Display Attributes</strong>. * represents mandatory fields."; ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header">
                        <h5>Presentation</h5>
                        <div class="card-header-right">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>
                    <div class="p-2 mt-2">
                        <div class="row mb-3">
                            <div class="col-lg-1">
                                <span class="col-form-label ml-2" > Presentation<b class="requried">*</b>  </span>
                            </div>
                            <div class="col-lg-2">
                                <select aria-label="Ect Presentation" id="selectPresentation" onchange="changePresentation(this.value);" class="form-control form-select js-example-basic-single">
                                    <option><?php echo $presentation_name != "" ?  $presentation_name : 'Choose'; ?></option>
                                    <?php
                                    foreach ($data_presentation as $list) { ?>
                                        <option <?php if($list['id'] == $_GET['PID'] ){ echo "selected";
} ?> value="<?php echo $list['id']; ?>"><?php $list['presentation_name']; echo $list['presentation_name']; ?> </option>
                                    <?php }

                                    ?>
                                    <option value="add">Add New</option>
                                </select>

                                <div  style="display: none;color: red;" id="table_err"> Please Select Any List</div>

                            </div>

                            <div class="col-lg-1">
                                <span  class="col-form-label ml-2"> Pages<b class="requried">*</b> </span>

                            </div>
                            <div class="col-lg-2">

                                <select aria-label="Ect Presentation Page" id="selectPresentationPage" onchange="changePresentationPage(this.value);" class="form-select js-example-basic-single">
                                    <option><?php echo 'Choose'; ?></option>
                                    <?php
                                    foreach ($data_presentation_pages as $pages) { ?>
                                        <option <?php if($pages['id'] == $_GET['PAGEID'] ){ echo "selected";
} ?> value="<?php echo $pages['id']; ?>"><?php echo $pages['header_name']; ?> </option>
                                    <?php } if($_GET['PID']!=""){ ?>

                                        <option value="add">Add New</option>
                                    <?php } ?>
                                </select>
                                <div  style="display: none;color: red;" id="display_list_err"> Please Select Any List</div>
                            </div>
                            <div class="col-lg-3"></div>
                            <div class="col-lg-3">

                            <?php if($_GET['PID']!="" && $_GET['PAGEID']!="") {?>

                                    <button type="button" onclick="dynamicinsertpopup('COM_INSERT_PRESENTATION_CONTENT')" class="btn btn-success float-right "> + ADD BLOCK</button>

                            <?php } ?>
                            <?php if($_GET['PID']!="") {?>

                                <button type="button" onclick="dynamicinsertpopup('COM_INSERT_PRESENTATION_CONTENT')" class="btn btn btn-primary float-right  m-r-5">Perview</button>

                            <?php } ?>
                        </div>
                        </div>
                        <div class="card card-custom gutter-b p-3" id="cloumn3">
                            <div class="card-header">
                                <div class="card-title">
                                    <span class="card-icon"><i class="flaticon2-chart text-primary"></i></span>
                                    <h3 class="card-label"><?php echo $presentation_array['0']['header_name'] ?></h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <!--begin: Pricing-->
                                    <?php

                                    $loop = 1;
                                    foreach ($presentation_array as $key => $data) {
                                        $required_divisions = count((array)$data['data']);
                                        if(!in_array($required_divisions, $div_count)){
                                            $value = $div_count_array[4];
                                        }
                                        else{
                                            $value = $div_count_array[$required_divisions];
                                        }
                                        $border = " border-x-0 border-x-md border-y border-y-md-0";
                                        $column =  "col-md-".$value." col-xxl-".$value;
                                        foreach ($data['data'] as  $content) {

                                            ?>

                                            <div class="<?php echo $column; if($i!=0 && $i!=$required_divisions) { echo "";
} ?>">
                                                <div class="float-right">
                                                    <button type="button" onclick="dynamiceditpopup('COM_INSERT_PRESENTATION_CONTENT',<?php echo "'".deocdeId("id:".$content[id])."'" ?>)" class="btn btn-sm btn-clean btn-icon btn-icon-sm"   title="Edit Datails" ><i  class="fa fa-edit"></i></button>
                                                </div>
                                                <div class="pt-30 pt-md-25 pb-15 px-5 text-center">
                                                    <!--begin::Icon-->
                                                    <?php if($i!=2 ) { ?>
                                                        <div class="d-flex flex-center position-relative mb-25">
                                                            <?php if($content['image']!="")
                                                            {
                                                                echo "<img src='uploads/all/".$content['image']."' width='300px'  alt='Attachment Preview' />";
                                                            }
                                                            ?>

                                                        </div>
                                                    <?php } ?>
                                                    <!--end::Icon-->

                                                    <!--begin::Content-->
                                                    <span class="font-size-h1 d-block d-block font-weight-boldest text-dark-75 py-2"> <?php echo $content['title']; ?></span>
                                                    <h4 class="font-size-h6 d-block d-block font-weight-bold mb-7 text-dark-50"><?php echo $content['sub_header']; ?></h4>
                                                    <p class="mb-15 d-flex flex-column">
                                                        <?php echo $content['title']; ?>
                                                        <?php echo $content['description']; ?>
                                                    </p>
                                                    <?php if($content['button_flag'])
                                                    {
                                                        echo '<a href="'.$content['button_url'].'" class="btn btn-primary text-uppercase font-weight-bolder px-15 py-3">'.$content['button_label'].'</a>';
                                                    }
                                                ?> </div>
                                            </div>
                                        <?php  }

                                    }?>

                                </div>

                                <!--end: Pricing-->

                            </div>
                        </div>
                    </div>
                    <!--end::Card-->
                    <div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
                        <div class="modal-dialog " >
                            <div class="modal-content"  id="popup2">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Presentation

                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <i aria-hidden="true" class="ki ki-close"></i>
                                    </button>
                                </div>
                                <div class="modal-body" >

                                    <form class="kt-form kt-form--label-right" action="" method="post">
                                        <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                                            <div class="form-group row">
                                                <div class="col-lg-12">
                                                    <span>Presentation Name</span>
                                                    <textarea aria-label="Presentation Name" class="form-control" name="presentation_name" id="comment"></textarea>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="modal-footer">
                                        <input type="hidden" name="submit_presentation" value="submit">
                                        <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-dismiss="modal">Close</button>
                                        <button  class="btn btn-primary mr-2">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="kt_modal_KTDatatable_remote2" class="modal fade" role="dialog" aria-hidden="true">
                    <div class="modal-dialog " >
                        <div class="modal-content"  id="popup3">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Presentation Header

                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i aria-hidden="true" class="ki ki-close"></i>
                                </button>
                            </div>
                            <div class="modal-body" >

                                <form class="kt-form kt-form--label-right" action="" method="post">
                                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <span>Header Name</span>
                                                <input aria-label="Header Name" type="text" class="form-control" name="header_name">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <span>Display Order</span>
                                                <input aria-label="Display Sequence" type="text" class="form-control" name="display_sequence">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="modal-footer">
                                    <input type="hidden" name="submit_header" value="submit">
                                    <input type="hidden" id="parent_id" name="parent_id" value="">
                                    <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-dismiss="modal">Close</button>
                                    <button  class="btn btn-primary mr-2">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl" >
                <div class="modal-content"  id="popup24">

                </div>
            </div>
        </div>

