<?php 
function menudisplaytop($menuarray,$type){
    $str .= '<ul class="pcoded-submenu">';
    foreach ($menuarray as $menuarrayvalue) {
        $child = count($menuarrayvalue['child']);
        $link = $child >0 ?  'javascript:;' : 'index.php?option='.$menuarrayvalue['Page_Name'];
        $drop =  $child >0 ?  'pcoded-hasmenu' : '';
            //$icon = ($menuarrayvalue['Menu_Icon']== '' )?'kt-menu__link-bullet kt-menu__link-bullet--dot' : $menuarrayvalue['Menu_Icon'].'icon-xl';
        $icon = "";
        $str .= '<li class="'.$drop.'" >
                <a href="'.$link.'" class="waves-effect waves-dark"> &nbsp;<i class="'.$icon.'"><span></span></i><span class="pcoded-mtext">'.$menuarrayvalue['Label'].'</span>';
        
        $str.='</a>';
        if(count($menuarrayvalue['child'])>0){
            $str.=menudisplaytop($menuarrayvalue['child'],'right');
        }
        $str.='</li>';
    }
    $str.='</ul>';
    return $str;
}


?>
<script type="text/javascript">
    function checkinattendence(msg){
        //alert(dat+'/'+tim);
        //str = 'date='+dat+'&time='+tim;
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=employeeclocks',
            success: function(response) {
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
                        }).then(function(result) {
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
    <div class="nav-list">
        <div class="pcoded-inner-navbar main-menu">
                <?php if (count($toplistnav) >0 && in_array('TNL', $_SESSION['role_nav'])){ foreach ($toplistnav as  $listnav) {?>
                       <!--  <ul class="pcoded-item pcoded-left-item">
                            <li class="pcoded-hasmenu ">
                                <a href="javascript:void(0)" class="waves-effect waves-dark">
                                    <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                                    <span class="pcoded-mtext">Dashboard</span>
                                </a>
                            </li>
                        </ul> -->
                <?php } } ?>
                <?php  if (count($topnav) >0 && in_array('TN', $_SESSION['role_nav'])){ foreach($topnav as $nav){    $child = count($nav['child']);
                        $link = $child >0 ?  'javascript:;' : 'index.php?option='.$nav['Page_Name'];
                        $drop =  $child >0 ?  'pcoded-hasmenu ' : ''; 
			$nav['Menu_Icon'] = $nav['Menu_Icon']!='' ? $nav['Menu_Icon'] : "icon-control-play";                                     
                ?>
                        <ul class="pcoded-item pcoded-left-item">
                            <li class="<?php echo  $drop; ?>">
                                <a href="<?php echo $link; ?>" class="waves-effect waves-dark">
                                    <span class="pcoded-micon">
					<?php if(strpos($nav['Menu_Icon'], "svg")){ ?>
                            <span class="svg-icon menu-icon">
                                <?php echo $nav['Menu_Icon']; ?>
                            </span>
                        <?php }else{ ?>                                 
                            <i class="<?php echo $nav['Menu_Icon']; ?>"></i> 
                        <?php } ?>
                        <!-- <i class="feather icon-sidebar"></i> -->
				    </span>
                                    <span class="pcoded-mtext"><?php echo $nav['Label']; ?></span>
                                </a>
                            
                            <?php if(count($nav['child'])>0){  echo menudisplaytop($nav['child'],'left');  } ?>
                            </li>
                        </ul>

                <?php } } ?>
                <?php if (count($topnavcenter) >0  && in_array('TNC', $_SESSION['role_nav'])){ foreach ($topnavcenter as  $nav) {
                        $child = count($nav['child']);
                    $listnav_pagename = $nav['Page_Name'];
                    if($_SESSION['userid']==50 && $listnav['Page_Name']=='COM_BULK_PROJECT_TIMESHEET'){ $listnav_pagename  = "COM_BULK_HR_PROJECT_TIMESHEET"; }
                    if($_SESSION['userid']==100 && $listnav['Page_Name']=='COM_BULK_PROJECT_TIMESHEET' ){ $listnav_pagename  = "COM_BULK_FINANCE_PROJECT_TIMESHEET"; }                  
                    $link = $child >0 ?  'javascript:;' : 'index.php?option='.$listnav_pagename;
                    $drop =  $child >0 ?  'menu-toggle' : '';
                ?>
                       <!--  <ul class="pcoded-item pcoded-left-item">
                            <li class="<?php echo  $drop; ?>">
                                <a href="<?php echo $link; ?>" class="waves-effect waves-dark">
                                    <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                                    <span class="pcoded-mtext"><?php echo $nav['Label']; ?></span>
                                </a>
                            
                            <?php if(count($nav['child'])>0){  echo menudisplaytop($nav['child'],'left');  } ?>
                            </li>
                        </ul> -->

                <?php } } ?>
    </div>
</div>
</nav>
<!-- [ navigation menu ] end -->