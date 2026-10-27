<?php
$menu_id = $_GET['MENU_ID'] !='' ? $_GET['MENU_ID'] :$saidnav[1]['id'];
$submenu_id = $_GET['SUB_MENU_ID'] !='' ? $_GET['SUB_MENU_ID'] :'';

?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">

        <div class="card card-custom gutter-b close-burger">
            <!--begin::Body-->
            <div class="card-body" style="margin-right: auto;margin-left: auto;">
                <ul class="dashboard-tabs nav nav-pills nav-danger row row-paddingless m-0 p-0 flex-column flex-sm-row">
                    <!--begin::Item-->
                    <?php foreach($saidnav as $nav){
                        $child = count((array)$nav['child']);
                        $link = $child >0 ?  'javascript:;' : 'index.php?option='.$nav['Page_Name'] ;
                        $icon = ($nav['Menu_Icon'] == '') ?'' : $nav['Menu_Icon'];
                        $toggle_name = $child > 0 ?  '1' : '0' ;
                        $active = $menu_id!=$nav['id'] ? '' : 'active';
                        $shadow =  $menu_id!=$nav['id'] ? 'px-4' : 'shadow';
                        ?>
                        <li class="nav-item <?php echo $shadow; ?>">
                            <a class="nav-link <?php echo $active; ?> border d-flex w-120px flex-grow-1 rounded flex-column align-items-center"  href="index.php?option=COM_LIST_LANDING_PAGES&MENU_ID=<?php echo $nav['id']; ?>">
                                <span class="nav-icon py-2 w-auto">
                                    <span class="svg-icon svg-icon-3x svg-icon-primary">


                                    <!--end::Svg Icon-->
                                    <?php echo $icon; ?>

                                </span>
                            </span>
                            <span class="nav-text font-size-lg py-2 font-weight-bold text-center"><?php echo $nav['Label']; ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="tab-content m-0 p-0">
        <div class="tab-pane active" id="tab_forms_widget_1" role="tabpanel">
            <?php
            $child = count((array)$saidnav[$menu_id]['child']);
            if($child > 0){ ?>
                <div class="card card-custom gutter-b">
                    <div class="card-body" style="margin-right: auto;margin-left: auto;">
                        <ul class="dashboard-tabs nav nav-pills nav-info row row-paddingless m-0 p-0 flex-column flex-sm-row">
                            <!--begin::Item-->
                            <?php foreach($saidnav[$menu_id]['child'] as $subnav){
                                $icon = ($subnav['Menu_Icon'] == '') ?'' : $subnav['Menu_Icon'];
                                $subactive = $submenu_id!=$subnav['id'] ? '' : 'active';
                                ?>
                                <li class="nav-item ">
                                    <a class="nav-link <?php echo $subactive; ?> border d-flex w-120px flex-grow-1 rounded flex-column align-items-center"  href="index.php?option=COM_LIST_LANDING_PAGES&MENU_ID=<?php echo $menu_id; ?>&SUB_MENU_ID=<?php  echo $subnav['id']; ?>">
                                        <span class="nav-icon py-2 w-auto">
                                            <span class="svg-icon svg-icon-3x svg-icon-primary">


                                                <?php echo $icon; ?>
                                            </span>
                                        </span>
                                        <span class="nav-text font-size-lg py-2 font-weight-bold text-center"><?php echo $subnav['Label']; ?></span>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <?php if($submenu_id!=""){ ?>
                    <div class="tab-content m-0 p-0">
                        <div class="tab-pane active" id="tab_forms_widget_1_2" role="tabpanel">
                            <?php
                            $_GET['option'] = $saidnav[$menu_id]['child'][$submenu_id]['Page_Name'];
                            if($_GET['option']!=""){
                                include_once "includes/controller.php";
                                $dispage = str_replace("views/", '', $disptemp);
                                include_once $dispage;
                            }else{
                                include_once "views/custom_wizard_dash_board.php";
                            }
                            ?>
                        </div>
                    </div>
                <?php } ?>
            <?php }else{


                $_GET['option'] = $saidnav[$menu_id]['Page_Name'];
                if($_GET['option']!=""){
                    include_once "includes/controller.php";
                    $dispage = str_replace("views/", '', $disptemp);
                    include_once $dispage;
                }else{
                    include_once "views/custom_wizard_dash_board.php";
                }
            }
            ?>
        </div>
    </div>
</div>
</div>
