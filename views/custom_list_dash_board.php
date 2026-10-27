<?php

$active = "active";
$color = 'style="color: #000;"';
$dash = isset($_GET['dash_borad']) ? $_GET['dash_borad'] : 0;

$nowrole = $_SESSION['working_role_short_code'];

$pass_module = array();
if(!in_array(SUPERADMIN_ROLE,(array)$_SESSION['role_ids'])){
    if($nowrole==ADMIN_ROLES){
        $nowrole = $_SESSION['module_setp'][$_SESSION['work_module_setup_id']]['role_admin_short_code'];
    }
    $pass_module_json = $_SESSION['allroles'][$nowrole]['dashboard_url'];
    $pass_module_details = json_decode($pass_module_json, 1);
    if($pass_module_json == ""){
        //no dashboard page
    }else{

        foreach($pass_module_details as   $keydash=>$dashh){
            $pass_module[] = array('name'=>$keydash,'page'=>$dashh);
        }
    }


}else{
     $pass_module[] = array('name'=>'Office Management Dashboard','page'=>'custom_list_registered_groups');
}
$profile_json = $_SESSION['allroles'][$nowrole]['profile_section_display'];
$profile_details = json_decode($profile_json, 1);
?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <?php
            if($profile_details['status']=='Y' && $profile_details['position']=='top' && $_POST['ajaxpage'] !="Y"){
                include_once "custom_view_dashboard_leftnav_employee.php";
            }else{
        ?>
        <?php if(count((array)$pass_module)>1){ ?>
        <div class="example mb-10 mt-5 container-fluid">
            <ul class="nav md-tabs" id="myTab1">
                <?php foreach($pass_module as $dash_key => $dash_module){ ?>
                    <li class="nav-item"<?php if($dash != $dash_key ){ echo $style;
} ?> >
                        <a class="nav-link  <?php if($dash == $dash_key ){ echo $active;
} ?>"  href="index.php?dash_borad=<?php echo $dash_key; ?>"
 <?php if($dash != 1 ){ echo $color;
} ?>>
                            <span class="nav-icon"> <i class="la la-money"  <?php if($dash != $dash_key ){ echo $color;
} ?>></i></span>
                            <span class="nav-text"><?php echo $dash_module['name']; ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>
             <?php
            if($profile_details['status']=='Y' && $profile_details['bottom']=='top'){
                include_once "custom_view_dashboard_leftnav_employee.php";
            }
             ?>
            <div class="tab-content mt-5" id="myTabContent1">
            <?php $k=1; foreach($pass_module as $dash_key => $dash_module){ ?>
                    <div class="tab-pane  <?php if($dash == $dash_key ){ echo $active;
} ?> " aria-labelledby="kt_tabs_1_<?php echo $k; ?>" id="kt_tabs_1_<?php echo $k; ?>" role="tabpanel">
                        <?php
                            if(count((array)$dash_module['pass']) > 0){
                                foreach ($dash_module['pass'] as $getkey => $getvalue) {
                                    $_GET[$getkey] = $getvalue;
                                }
                            }
                            if($_POST['ajaxpage'] =='Y'){
                                include_once $dash_module['page'].".php";
                                 $ndcjdnsc = $dash_module['page'].".php";
                            }else{
                                include_once "views/".$dash_module['page'].".php";
                            }
                        ?>
                    </div>
            <?php $k++;
} ?>
        </div>
    </div>
    <?php }else{
        if(count((array)$pass_module['0']['pass']) > 0){
            foreach ($pass_module['0']['pass'] as $getkey => $getvalue) {
                $_GET[$getkey] = $getvalue;
            }
        }
        if($_POST['ajaxpage'] =='Y'){
            include_once $pass_module['0']['page'].".php";
            $ndcjdnsc = $pass_module['0']['page'].".php";
        }else{
            include_once "views/".$pass_module['0']['page'].".php";
        }
    }
} ?>
</div>
</div>
