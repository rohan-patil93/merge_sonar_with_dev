<?php
$emailurl = API."?mode=getemailgroupdetails";
$emailgroupdata = callAPI($emailurl);
$emailgroupdata = json_decode($emailgroupdata,1);
?>
<script>
    function dynamicinsertpopup(option,id){
        var idstr = "";
        if(id != ""){
            var idstr = "&Rid="+id;
        }
        jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option+idstr,
            type: "POST",
            success:function(data){
                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal("show");
            },
            error:function (){}
        });
    }
</script>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="card card-custom ">
                  <div class="card-header table-card-header mb-2">
                   <h5>Email Group</h5>
                   <div class="card-header-right table-card-header ">
                    <button type="button" class=" btn btn-success" onclick="dynamicinsertpopup('COM_INSERT_EMAIL_GROUPS','')"><i class="icofont icofont-plus icon-white "></i>Email Group </button>
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                      <i class="icofont icofont-reply"></i>
                      Back
                    </a>
                  </div>
                </div>

<div class="card-body">
<div class="row">
<?php foreach ($emailgroupdata as $email) {
$rid = deocdeId("id:".$email['id']);
?>

<div class="col-xl-<?php echo isset($_GET['PAGENAME'])?"4":"3"?>">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b card-stretch">
                            <!--begin::Body-->
                            <div class="card-body">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Pic-->
                                    <div class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1">
                                        <?php echo initials(ucwords($email['name'])); ?>
                                    </div>
                                    <!--end::Pic-->
                                    <!--begin::Info-->
                                    <div class="d-flex flex-column mr-auto">
                                        <!--begin: Title-->
                                        <a href="#" class="card-title text-hover-primary font-weight-bolder font-size-h5 text-dark mb-1"><?php echo ucwords($email['name']); ?> </a>
                                        <span class="text-muted font-weight-bold">Owner of the group <?php echo ucwords($email['owner']); ?></span>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Toolbar-->
                                    <div class="card-toolbar mb-auto">
                                        <div class="dropdown dropdown-inline" data-toggle="tooltip" title="Quick actions" data-placement="left">
                                            <a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                <i class="fa fa-ellipsis-h"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                <!--begin::Navigation-->
                                                <ul class="navi navi-hover">


                                                    <li class="navi-item ml-2">
                                                        <button type="button" onclick="dynamicinsertpopup('COM_INSERT_EMAIL_GROUPS','<?php echo $rid; ?>')"  class="navi-link btn p-0 border-0 bg-transparent text-left" style="cursor: pointer;">
                                                        <span class="navi-text"><i class="fa fa-edit"></i>Edit</span>
                                                        </button>
                                                    </li>
                                                    <li class="navi-item ml-2">
                                                        <button type="button" onclick="dynamicinsertpopup('COM_INSERT_EMAIL_GROUP_EMAILS','<?php echo $rid; ?>')" class="navi-link btn p-0 border-0 bg-transparent text-left" style="cursor: pointer;">
                                                            <span class="navi-text"><i class="fa fa-user-plus"></i> Insert Member</span>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Toolbar-->
                                </div>
                                <p class="mb-7 mt-3"><?php echo $email['description']; ?></p>

                                <div class="d-flex flex-wrap">
                                    <!--begin: Item-->
                                    <div class="mr-12 d-flex flex-column mb-7 text-left">
                                        <span class="d-block font-weight-bold mb-0">Start Date</span>
                                        <span class="font-weight-bold btn-upper btn-text f-12"><?php echo date("d M, y", strtotime($email['start_date'])); ?></span>
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->

                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex flex-column flex-lg-fill mb-7 text-right">
                                        <span class="font-weight-bolder mb-0">Members</span>

                                        <div class="symbol-group symbol-hover">
                                            <div class="symbol-group symbol-hove ml-2r">
                                            <?php $mk=0; foreach ($email['users'] as $member) { ?>
                                                <div class="symbol symbol-35 symbol-circle" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $member['name']; ?>">
                                                    <span class="symbol-label font-weight-bold"> <?php echo initials($member['name']); ?></span>
                                                </div>
                                            <?php $mk++; if($mk == '3'){ break; } ?>
                                            <?php } ?>
                                            <?php if($email['count'] > 3){ ?>
                                                    <span class="symbol-label font-weight-bold">+<?php echo $email['count'] - 3; ?></span>
                                            <?php } ?>

                                            </div>
                                        </div>


                                    </div>
                                    <!--end::Item-->
                                </div>
                                <!--end::Blog-->
                            </div>
                            <!--end::Body-->
                            <!--begin::Footer-->
                            <div class="card-footer d-flex align-items-center">
                                <div class="d-flex row">
                                    <div class="d-flex align-items-center mr-7 col-lg-8">
                                        <span class="svg-icon svg-icon-gray-500">

                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z" fill="#000000" />
                                                    <path d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z" fill="#000000" opacity="0.3" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                        <a href="#" class="font-weight-bolder text-primary ml-2"><?php echo $email['count'] ?> Tasks</a>
                                    </div>
                                    <?php $uu = "index.php?option=COM_LIST_EMAIL_GROUP_EMAIL&RID=".deocdeId($email['id']); ?>
                                    <a href="<?php echo $uu; ?>" class="col-lg-4">    <button type="button" class="btn btn-primary btn-sm text-uppercase font-weight-bolder mt-5 mt-sm-0 mr-auto mr-sm-0 ml-sm-auto">details</button> </a>

                                </div>
                                </div>
                            <!--end::Footer-->
                        </div>
                        <!--end::Card-->
                    </div>
<?php } ?>

</div>
</div>
</div>
</div>
</div>
</div></div>


