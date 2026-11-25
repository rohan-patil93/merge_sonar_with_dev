<?php
defined('URL_OPTION_PREFIX') || define('URL_OPTION_PREFIX', URL_OPTION_PREFIX);
defined('URI_JAVASCRIPT_VOID') || define('URI_JAVASCRIPT_VOID', URI_JAVASCRIPT_VOID);
function menudisplaytop($menuarray)
{
    $str = '<ul class="pcoded-submenu">';
    foreach ($menuarray as $menuarrayvalue) {
        $child = count($menuarrayvalue['child']);
        $link = $child > 0 ? URI_JAVASCRIPT_VOID : URL_OPTION_PREFIX . $menuarrayvalue['Page_Name'];
        $drop = $child > 0 ? 'pcoded-hasmenu' : '';
        $icon = "";
        $str .= '<li class="' . $drop . '" >
                <a href="' . $link . '" class="waves-effect waves-dark"> &nbsp;<i class="' . $icon . '"><span></span></i><span class="pcoded-mtext">' . $menuarrayvalue['Label'] . '</span>';

        $str .= '</a>';
        if (count($menuarrayvalue['child']) > 0) {
            $str .= menudisplaytop($menuarrayvalue['child']);
        }
        $str .= '</li>';
    }
    $str .= '</ul>';
    return $str;
}


?>
<script type="text/javascript">
    function checkinattendence(msg) {
        //alert(dat+'/'+tim);
        //str = 'date='+dat+'&time='+tim;
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=employeeclocks',
            success: function (response) {
                console.log(response);
                //alert(response);
                //$('.success').html(response);
                //document.getElementById("myForm").submit();
                swal.fire({
                    title: msg,
                    //text: "Do you want to close ? ",
                    type: "success",
                    buttonsStyling: false,
                    confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
                    confirmButtonClass: "btn btn-danger",
                    showCancelButton: false,
                    cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                    cancelButtonClass: "btn btn-default"
                }).then(function (result) {
                    if (result.value) {
                        $('#kt_modal_KTDatatable_remote').modal('hide');
                        window.location.reload();
                    }
                });
            }
        });
    }
</script>
<!-- [ navigation menu ] start -->
<nav class="pcoded-navbar">
    <div class="pt-2 bg-custom-color">
        <div class="sale-card  m-t-30">
            <div class="card-block text-center m-t-30">
                <a href="index.php">
                    <img class="img-fluid" src="uploads/all/file_1644577403_6206427b53184.png" alt="Theme-Logo">
                </a>
            </div>

            <div class="card-block text-center mt-4">
                <div data-label=""
                    class="radial-bar radial-bar-100 radial-bar-lg radial-bar-danger mb-2 mt-0 custom-radial">
                    <img src="uploads/all/<?php echo $_SESSION['employee']['image']; ?>" class="img-120 " alt="" />
                </div>
                <div class="mt-1 custom-bell">
                    <ul>
                        <li class="header-notification  m-r-20" data-bs-toggle="tooltip" data-placement="bottom"
                            title="" data-bs-original-title="Notification">
                            <div class="dropdown-primary dropdown">
                                <div class="displayChatbox dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="ti-bell"></i>
                                    <span class="badge bg-c-red">38</span>
                                </div>

                            </div>
                        </li>
                    </ul>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <h6>
                            <a href="#"
                                class="text-dark-75 text-hover-primary font-size-h6 font-weight-bold mr-3 f-14 f-w-600 text-white"><?php echo $_SESSION['employee']['display_name']; ?>
                            </a>
                        </h6>
                        <h6 class="f-14"><?php ?></h6>
                        <h6 class="f-14"><?php ?></h6>
                        <h6 class="text-white f-14"><?php echo 'CFO'; ?></h6>
                        <h6 class="text-white f-14"><?php echo 'Hydrabad'; ?></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="nav-list border-top">
        <div class="pcoded-inner-navbar main-menu">

            <?php if (count($toplistnav) > 0 && in_array('TNL', $_SESSION['role_nav'])) {
                foreach ($toplistnav as $listnav) { ?>
                <?php }
            } ?>
            <?php if (count($topnav) > 0 && in_array('TN', $_SESSION['role_nav'])) {
                foreach ($topnav as $nav) {
                    $child = count($nav['child']);
                    $link = $child > 0 ? URI_JAVASCRIPT_VOID : URL_OPTION_PREFIX . $nav['Page_Name'];
                    $drop = $child > 0 ? 'pcoded-hasmenu ' : '';
                    $nav['Menu_Icon'] = $nav['Menu_Icon'] != '' ? $nav['Menu_Icon'] : "icon-control-play";
                    ?>
                    <ul class="pcoded-item pcoded-left-item">
                        <li class="<?php echo $drop; ?>">
                            <a href="<?php echo $link; ?>" class="waves-effect waves-dark">
                                <span class="pcoded-micon">
                                    <?php if (strpos($nav['Menu_Icon'], "svg")) { ?>
                                        <span class="svg-icon menu-icon">
                                            <?php echo $nav['Menu_Icon']; ?>
                                        </span>
                                    <?php } else { ?>
                                        <i class="<?php echo $nav['Menu_Icon']; ?>"></i>
                                    <?php } ?>
                                </span>
                                <span class="pcoded-mtext"><?php echo $nav['Label']; ?></span>
                            </a>

                            <?php if (count($nav['child']) > 0) {
                                echo menudisplaytop($nav['child']);
                            } ?>
                        </li>
                    </ul>

                <?php }
            } ?>
            <?php if (count($topnavcenter) > 0 && in_array('TNC', $_SESSION['role_nav'])) {
                foreach ($topnavcenter as $nav) {
                    $child = count($nav['child']);
                    $listnav_pagename = $nav['Page_Name'];
                    if ($_SESSION['userid'] == 50 && $listnav['Page_Name'] == 'COM_BULK_PROJECT_TIMESHEET') {
                        $listnav_pagename = "COM_BULK_HR_PROJECT_TIMESHEET";
                    }
                    if ($_SESSION['userid'] == 100 && $listnav['Page_Name'] == 'COM_BULK_PROJECT_TIMESHEET') {
                        $listnav_pagename = "COM_BULK_FINANCE_PROJECT_TIMESHEET";
                    }
                    $link = $child > 0 ? URI_JAVASCRIPT_VOID : URL_OPTION_PREFIX . $listnav_pagename;
                    $drop = $child > 0 ? 'menu-toggle' : '';
                    ?>




                <?php }
            } ?>




        </div>
    </div>

    <div class="p-4 border-top bg-custom-color pb-5">
        <div class="nav-left float-left">
            <a href="index.php?option=com_logout" class="text-white f-w-600">
                <i class="ti-power-off mr-10"></i> Logout
            </a>
        </div>
        <ul class="nav-right float-right">
            <li class="header-notification m-r-8" data-bs-toggle="tooltip" data-placement="bottom" title=""
                data-bs-original-title="Inbox">
                <a class="dropdown-primary d-block" style="text-decoration:none;color:inherit;"
                    href="index.php?option=COM_LIST_INBOX">
                    <div class="" data-bs-toggle="dropdown">
                        <i class="ti-email text-white"></i>
                        <span class="badge bg-c-green customcount">2</span>
                    </div>
                </a>
            </li>
            <li class="header-notification m-r-10" data-bs-toggle="tooltip" data-placement="bottom" title=""
                data-bs-original-title="Announcement">
                <a class="dropdown-primary d-block" style="text-decoration:none;color:inherit;"
                    href="index.php?option=COM_VIEW_ANNOUNCEMENTS">
                    <div class="" data-bs-toggle="dropdown">
                        <i class="ti-announcement"></i>
                    </div>
                </a>
            </li>
            <li class="header-notification" data-bs-toggle="tooltip" data-placement="bottom" title=""
                data-bs-original-title="Setting">
                <a class="dropdown-primary d-block" style="text-decoration:none;color:inherit;"
                    href="index.php?option=COM_LIST_APPLICATION_MENU_SETTINGS">
                    <div class="" data-bs-toggle="dropdown">
                        <i class="ti-settings"></i>
                    </div>
                </a>
            </li>

        </ul>
    </div>

</nav>
<!-- [ navigation menu ] end -->
