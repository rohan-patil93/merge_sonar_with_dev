<?php
    $allviews = array(
    'EMP'=>array('name'=>'Employee','page'=>'custom_insert_employe_details','icon'=>'ti-user','type'=>'N'),
    'SAL'=> array('name'=>'Salary','page'=>'custom_insert_salary','icon'=>'ti-money','type'=>'N'),
    'ATT'=> array('name'=>'Attendance','page'=>'custom_view_employe_attendance','icon'=>'ti-calendar','type'=>'N'),
    'LEA'=> array('name'=>'Leave','page'=>'custom_view_employe_leave','icon'=>'ti-rocket','type'=>'N'),
    'PAR'=>array('name'=>'Payroll','page'=>'custom_link_employe_payroll','icon'=>'ti-user','type'=>'N'),
    'UMA'=> array('name'=>'User & Manager','page'=>'custom_link_employe_user','icon'=>'ti-link','type'=>'N')

    );
    $PR = $_GET['PR'] !="" ? $_GET['PR'] : 'EMP';
    $EMPID = $_GET['EMPID'] !="" ? $_GET['EMPID'] : '0';

    if($EMPID != 0){
        $main_ids = encodeId($EMPID);
        $main_id = explode(":",$main_ids);
        $_GET['newempid'] = $main_id['1'];

        $url = API."?mode=togetnewinsertemloyeedetails&empid=".$_GET['newempid'];
        $cudata = callAPI($url);
        $empalldata = json_decode($cudata,1);
        $empdata = $empalldata['employee'];
        if(count((array)$empalldata['employee'])>0){
            $allviews['EMP']['type'] = 'Y';
        }
        if(count((array)$empalldata['salary'])>0){
            $allviews['SAL']['type'] = 'Y';
            $_GET['SEid'] = $EMPID;
            $allviews['ATT']['type'] = 'Y';
        }
        if(count((array)$empalldata['employee_balance'])>0){
            $allviews['LEA']['type'] = 'Y';
        }
        if(count((array)$empalldata['application_users'])>0){
            $allviews['UMA']['type'] = 'Y';

        }
        if(count((array)$empalldata['pay_area_employees'])>0){
            $allviews['PAR']['type'] = 'Y';
        }


    }
    $_SESSION['apply_js']['multiselest'] =1;

?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row workflow-step text-center mt-5 mb-5">
            <?php $k=1; foreach($allviews  as $viewkey=>$allview){
                $link = "#";
                  if($EMPID !=0){
                   $link = "index.php?option=COM_INSERT_NEW_EMPLOYEE_FULL_DETAILS&EMPID=".$EMPID."&PR=".$viewkey;
               }
            ?>
            <input type="hidden" name="newempid" id="newempid" value="<?php echo $_GET['newempid']; ?>">
            <input type="hidden" name="newempidcode" id="newempidcode" value="<?php echo $_GET['EMPID']; ?>">
                <div class="col-lg-2">
                    <?php
                    $crp = "primary";

                    if($allview['type'] == "Y"){
                        $crp = "success";
                    }
                    if($PR == $viewkey){
                        $crp = "warning";
                    } ?>

                    <div class="work-box bg-<?php echo $crp; ?>" style="border: 0px;">
                       <a href="<?php echo $link; ?>"> <i class="<?php echo $allview['icon']; ?>"></i> </a>
                    </div>
                    <?php if(count((array)$allviews) > $k){ ?>
                    <div class="step-icon bg-primary"></div>
                    <?php  } ?>
                    <div class="font-size-lg mt-1"><?php echo $allview['name']; ?></div>

                </div>
            <?php $k++;
} ?>
        </div>
        <div class="row">
            <?php $k=1;
                foreach($allviews as $viewkey=>$allview){
                    $view = "views/".$allview['page'].".php";
                    if($PR == $viewkey){
            ?>
            <div>
            <?php    include_once $view; ?>
            </div>
            <?php
                }
                $k++;
}
            ?>
        </div>
    </div>
</div>
