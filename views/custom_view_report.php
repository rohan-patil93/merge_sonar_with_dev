 <?php
if(!isset($_GET['TYPE'])){
    $_GET['TYPE'] =  'MYR';
}
$groupmodulee = $_SESSION['default_module_group_id'];
if(isset($_GET['DGM']) && $_GET['DGM'] != ""){
    $groupmodulee = $_GET['DGM'];
}
$fld = $_GET['FLD'];
$data12["where"]['is_active'] = '0';
if($fld!=""){
}

$data = urlencode(json_encode($data12));
$itemurl = API."?mode=gettabledetailswithconditions&table=my_reports&data=".$data;
$item_data = callAPI($itemurl);
$row = json_decode($item_data, 1);
foreach($row as $rowdata){
    $list_breadcum[$rowdata['id']] = array('parent_id'=>$rowdata['parent_id'],'name'=>$rowdata['page_name'],'id'=>$rowdata['id'],'type'=>'MYR');
    if(($rowdata['parent_id'] == '0' && $fld=="") || ( $fld == $rowdata['parent_id'] && $rowdata['header'] == '')){
        $parent_data[] = $rowdata;
    }else{
        $submain_data[$rowdata['parent_id']][] = $rowdata;
    }
    $alllists[$rowdata['id']] = $rowdata;
}
$sub_data = array();
if($fld!=""){
    $sub_data = $submain_data[$fld];
}
if($fld!=""){
    $redid = $fld;
    for($i=10;$i>0;$i--){
        $breadcum[$i] = $list_breadcum[$redid];

        if($list_breadcum[$redid]['parent_id'] == 0){
            break;
        }
        $redid = $list_breadcum[$redid]['parent_id'];
    }
}
$breadcum[0] = array('id'=>'','name'=>'My Report','type'=>'MYR');
if($_GET['TYPE']=='SR'){
    $breadcum[0] = array('id'=>'','name'=>'Stantdard Report','type'=>'SR');
}

$data15["where"] = array('is_active' =>'0','company_id'=>$_SESSION['company_id']);
$data15["field"] = "id,display_name,image";
$data5 = urlencode(json_encode($data15));
$itemurl5 = API."?mode=gettabledetailswithconditions&table=employees&data=".$data5;
$item_data4 = callAPI($itemurl5);
$emplo5  = json_decode($item_data4, 1);

$employees_list = array();
foreach($emplo5 as $emplo){
    $employees_list[$emplo['id']] = $emplo;
}
 ?>
 <style>
.file-img {margin: 0px 0px 0px -18px; width: 15px;}
</style>
<script type="text/javascript">
    function deleteidrow(id,deleteid) {
        var delet = confirm('Are you sure want to delete report?');
        if(delet){
            $.ajax({
                url: "ajax.php",
                data:'page=deletemyreport&id='+id,
                type: "POST",
                success:function(data){
                    $(".row"+deleteid).remove();
                }
            });
        }else{
            return false;
        }
    }
    function movecopyfloder(report_id){
        jQuery.ajax({
            url: "views/custom_list_report_save_popup.php",
            data:'report_id='+report_id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function changeflodername(report_id,floder_name){
        jQuery.ajax({
            url: "views/custom_report_change_floder_name.php",
            data:'report_id='+report_id+'&floder_name='+floder_name,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function movetotrash(report_id){
        var  str = '';
        str = 'fid=is_active&table=my_reports&hid='+report_id+'&rid=1';
        console.log(str);
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=updatedynamicfied&'+str,
            success: function(response) {
                console.log(response);
                swal.fire({
                    title: "Report is move to trash successfully",
                    type: "success",
                    buttonsStyling: true,
                    confirmButtonText: "<i class='la la-hand-o-up'></i>OK",
                    confirmButtonClass: "btn btn-success",
                    showCancelButton: false

                }).then(function(result) {
                    if (result.value) {
                        $('#kt_modal_KTDatatable_remote').modal('hide');
                        window.location.reload();
                    }
                });
            }
        });
    }
    function copyreportsbyid(id) {
        $.ajax({
            url: "ajax.php",
            data:'page=copyreportsbyid&report_id='+id,
            type: "POST",
            success:function(data){
                $(".row"+deleteid).remove();
            }
        });
    }
</script>
 <div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card-header" >
            <h5> Report Builder </h5>
            <div class="card-header-right" >
                <a href="index.php?option=COM_INSERT_REPORT_ATTRIBUTES_NEW" class="btn btn-success mb-2 desktop-view">
              <i class="icofont icofont-plus icon-white "></i>
              Report            </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row ">
                <div class="col-lg-12">
                <?php if($_GET['FLD']==""){ ?>
                 <h3 class="f-18 f-w-600">Stantdard Report</h3>
                 <div class="row mt-3">
                    <?php foreach($parent_data as $pdata){
                        $count = count((array)$submain_data[$pdata['id']]);
                        if($pdata['report_type']=='SR'){ ?>
                    <div class="col-lg-3">
                        <div class="card bx-shadow">
                            <div class="card-block">
                                <div class="float-right">
                                    <div class="dropdown-primary dropdown open">
                                        <button class="btn waves-effect waves-light p-0" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
                                            <?php if($_SESSION['admin_type'] == 1){ ?>
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="changeflodername(<?php echo $pdata['id'].",'".$pdata['page_name']."'"; ?>)" ><?php echo $pdata['page_name']; ?><i class="ti-pencil m-l-10"></i></button>
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="movecopyfloder(<?php echo $pdata['id']; ?>)">Move to Folder</button>
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="movetotrash(<?php echo $pdata['id']; ?>)">Move to Trash</button>
                                          <?php } ?>
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="copyreportsbyid(<?php echo $pdata['id']; ?>)">Make a Copy</button>
                                        </div>
                                    </div>
                                </div>
                                <a href="index.php?option=COM_VIEW_REPORT&TYPE=MYR&FLD=<?php echo $pdata['id'] ?>">
                                <div class="align-items-center">
                                    <img src="assets/images/folder.png" class="img-20" alt="">
                                    <div class="d-block m-t-15">
                                        <h4 class="f-14 m-0 text-primary"><?php echo $pdata['page_name']; ?> <i class="ti-user f-14 text-dark m-l-10"></i></h4>
                                        <p class="m-0 mt-2 text-muted f-12 f-w-600">No of Files <?php echo $count; ?></p>
                                    </div>
                                </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php } ?>


                </div>
                <h3 class="f-18 f-w-600">My Report</h3>
                <div class="row mt-3">
                    <?php foreach($parent_data as $pdata){
                        $count = count((array)$submain_data[$pdata['id']]);
                        if($pdata['report_type']=='MYR'){ ?>
                    <div class="col-lg-3">
                        <div class="card bx-shadow">
                            <div class="card-block">
                                <div class="float-right">
                                    <div class="dropdown-primary dropdown open">
                                        <button class="btn waves-effect waves-light p-0" type="button" id="dropdown-2_2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="changeflodername(<?php echo $pdata['id'].",'".$pdata['page_name']."'"; ?>)" ><?php echo $pdata['page_name']; ?><i class="ti-pencil m-l-10"></i></button>
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="copyreportsbyid(<?php echo $pdata['id']; ?>)">Make a Copy</button>
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="movecopyfloder(<?php echo $pdata['id']; ?>)">Move to Folder</button>
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="movetotrash(<?php echo $pdata['id']; ?>)">Move to Trash</button>
                                            <a class="dropdown-item waves-light waves-effect" href="index.php?option=COM_INSERT_REPORT_ATTRIBUTES_NEW&Rid=<?php echo deocdeId("id:".$pdata['report_id']); ?>" >View</a>
                                        </div>
                                    </div>
                                </div>
                                <a href="index.php?option=COM_VIEW_REPORT&TYPE=MYR&FLD=<?php echo $pdata['id'] ?>">
                                <div class="align-items-center">
                                    <img src="assets/images/folder.png" class="img-20" alt="">
                                    <div class="d-block m-t-15">
                                        <h4 class="f-14 m-0 text-primary"><?php echo $pdata['page_name']; ?> <i class="ti-user f-14 text-dark m-l-10"></i></h4>
                                        <p class="m-0 mt-2 text-muted f-12 f-w-600">No of Files <?php echo $count; ?></p>
                                    </div>
                                </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php } ?>

                </div>

                <div class="card" style="display:none">
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-hover m-b-0">
                                <thead>
                                   <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Report Name</th>
                                        <th scope="col">Activity</th>
                                        <th scope="col">Created By</th>

                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="col">1</th>
                                        <td>Facebook</td>
                                        <td>19-11-2018</td>
                                        <td>
                                            <div class="d-inline-block align-middle">
                                                <img src="../files/assets/images/avatar-4.jpg" alt="user" class="img-radius img-40 align-top m-r-15">
                                                <div class="d-inline-block">
                                                    <h6>Shirley  Hoe</h6>
                                                    <p class="text-muted m-b-0">Sales executive , NY</p>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="dropdown-primary dropdown open float-right">
                                                <button class="btn waves-effect waves-light p-0" type="button" id="dropdown-2_3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="#">Action</a>
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="#">Another action</a>
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="#">Something else</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="#">Something else</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="col">2</th>
                                        <td>Facebook</td>
                                        <td>19-11-2018</td>
                                        <td>
                                            <div class="d-inline-block align-middle">
                                                <img src="../files/assets/images/avatar-4.jpg" alt="user" class="img-radius img-40 align-top m-r-15">
                                                <div class="d-inline-block">
                                                    <h6>Shirley  Hoe</h6>
                                                    <p class="text-muted m-b-0">Sales executive , NY</p>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="dropdown-primary dropdown open float-right">
                                                <button class="btn waves-effect waves-light p-0" type="button" id="dropdown-2_4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="#">Action</a>
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="#">Another action</a>
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="#">Something else</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="#">Something else</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="col">3</th>
                                        <td>Facebook</td>
                                        <td>19-11-2018</td>
                                        <td>
                                            <div class="d-inline-block align-middle">
                                                <img src="../files/assets/images/avatar-4.jpg" alt="user" class="img-radius img-40 align-top m-r-15">
                                                <div class="d-inline-block">
                                                    <h6>Shirley  Hoe</h6>
                                                    <p class="text-muted m-b-0">Sales executive , NY</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown-primary dropdown open float-right">
                                                <button class="btn waves-effect waves-light p-0" type="button" id="dropdown-2_5" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="#">Action</a>
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="#">Another action</a>
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="#">Something else</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="#">Something else</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php }else{ ?>
                    <div class="col-lg-12 page-body breadcrumb-page">
                        <div class="card borderless-card ">
                            <div class="card-block1 primary-breadcrumb" style="border-radius:6px;">
                                <div class="page-header-breadcrumb">
                                    <ul class="breadcrumb border-0" style="padding: 17px;color: #FFF;">
                                        <?php foreach($breadcum as $cum){ ?>
                                            <li class="breadcrumb-item">
                                                <a href="index.php?option=COM_VIEW_REPORT&TYPE=<?php echo $cum['type']; ?>&FLD=<?php echo $cum['id']; ?>" style="color: #FFF;"> <?php echo $cum['name']; ?> </a>
                                            </li>
                                        <?php } ?>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row col-lg-12">
                         <div class="row mt-3">
                    <?php
                    if(count((array)$parent_data)>0){
                    foreach($parent_data as $pdata){
                        $count = count((array)$submain_data[$pdata['id']]); ?>
                    <div class="col-lg-3">
                        <div class="card bx-shadow">
                            <div class="card-block">
                                <div class="float-right">
                                    <div class="dropdown-primary dropdown open">
                                        <button class="btn waves-effect waves-light p-0" type="button" id="dropdown-2_6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="changeflodername(<?php echo $pdata['id'].",'".$pdata['page_name']."'"; ?>)" ><?php echo $pdata['page_name']; ?><i class="ti-pencil m-l-10"></i></button>
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="copyreportsbyid(<?php echo $pdata['id']; ?>)">Make a Copy</button>
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="movecopyfloder(<?php echo $pdata['id']; ?>)">Move to Folder</button>
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="movetotrash(<?php echo $pdata['id']; ?>)">Move to Trash</button>
                                        </div>
                                    </div>
                                </div>
                                <a href="index.php?option=COM_VIEW_REPORT&TYPE=MYR&FLD=<?php echo $pdata['id'] ?>">
                                <div class="align-items-center">
                                    <img src="assets/images/folder.png" class="img-20" alt="">
                                    <div class="d-block m-t-15">
                                        <h4 class="f-14 m-0 text-primary"><?php echo $pdata['page_name']; ?> <i class="ti-user f-14 text-dark m-l-10"></i></h4>
                                        <p class="m-0 mt-2 text-muted f-12 f-w-600">No of Files <?php echo $count; ?></p>
                                    </div>
                                </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php } else { ?>
                     $_GET['RID'] = $_GET['FLD'];
                     include_once "views/".strtolower($alllists[$_GET['FLD']]['page_url']).".php";
                    <?php } ?>

                </div>
                        <?php if(count((array)$sub_data)>0){ ?>
                             <div class="card">
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table table-hover m-b-0">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Report Name</th>
                                        <th scope="col">Activity</th>
                                        <th scope="col">Created By</th>

                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i= 1;   foreach($sub_data  as $result){  ?>
                                    <tr>
                                        <th scope="col"><?php echo $i;?></th>
                                        <td><a href="index.php?option=COM_VIEW_MY_REPORT&Rid=<?php echo deocdeId("id:".$result['report_id']);  ?>"><?php echo $result['page_name']; ?></a></td>
                                        <td><?php echo $result['updated_date']; ?></td>
                                        <td>
                                            <div class="d-inline-block align-middle">

                                               <?php if($employees_list[$result['emp_id']]['image']) {?>
                                                            <img src="uploads/all/<?php echo $employees_list[$result['emp_id']]['image']; ?>"  class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                                        <?php } else { ?>
                                                            <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($employees_list[$result['emp_id']]['display_name']); ?></span>
                                                        <?php } ?>
                                                <div class="d-inline-block">
                                                    <h6><?php echo $employees_list[$result['emp_id']]['display_name']; ?></h6>
                                                    <p class="text-muted m-b-0">Sales executive , NY</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown-primary dropdown open float-right">
                                                <button class="btn waves-effect waves-light p-0" type="button" id="dropdown-2_7" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
                                                    <a class="dropdown-item waves-light waves-effect d-block" href="index.php?option=COM_VIEW_MY_REPORT&Rid=<?php echo deocdeId("id:".$result['report_id']);  ?>">Preview</a>
                                                    <a class="dropdown-item waves-light waves-effect d-block"href="index.php?option=COM_INSERT_REPORT_ATTRIBUTES_NEW&Rid=<?php echo deocdeId("id:".$result['report_id']);  ?>">Edit</a>

                                                    <div class="dropdown-divider"></div>

                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="copyreportsbyid(<?php echo $result['id']; ?>)">Make a Copy</button>
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="movecopyfloder(<?php echo $result['id']; ?>)">Move to Folder</button>
                                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="movetotrash(<?php echo $result['id']; ?>)">Move to Trash</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $i++;
} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php /**/ ?>
