<?php if(count((array)$saidnav) >0 ){ ?>
    <div class="row mb-2">
        <ul class="nav md-tabs m-l-10" id="myTab1">
            <?php $km = 0;  foreach($saidnav as $nav){  $activtb ='';
                if($nav['Page_Name'] == $_GET['SUBO']){
                    $activtb = "active";
                    $option_name = $nav['Page_Name'];
                }elseif($km==0 && $_GET['SUBO']==""){
                    $activtb = "active";
                    $option_name = $nav['Page_Name'];
                }
                ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $activtb; ?>"  href="index.php?option=COM_VIEW_BUTTON_DASHBOARDS&SUBO=<?php echo $nav['Page_Name']; ?>">
                    <span class="nav-icon"> <i class="<?php echo $nav['Menu_Icon']; ?>"></i></span> &nbsp;
                    <span class="nav-text"><?php echo $nav['Label']; ?></span>
                </a>
            </li>
            <?php $km++;
} ?>
        </ul>
    </div>
<?php }

$_GET['option'] = $option_name;
if($_GET['option']!=""){
    if($_GET['popup']=='Y'){
        include_once "../includes/controller.php";
    }else{
        include_once "includes/controller.php";
    }
    $dispage = str_replace("views/", '', $disptemp);
    $refrashpagename =$dispage;
    include_once $dispage;
}else{
    if(count((array)$_POST) !='0' && $_SESSION['ajaxpage']!=""){
        $refrashpagename =$_SESSION['ajaxpage'];
        include_once $_SESSION['ajaxpage'];

    }else{
        $_POST['ajaxpage'] = "Y";
        $refrashpagename ="custom_list_dash_board.php";
        include_once "custom_list_dash_board.php";
    }
}
?>
