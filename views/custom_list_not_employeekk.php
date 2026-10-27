<script>
   function displayitemdata(option,id){
     $.ajax({
         url: "views/view_roll_back1.php",
         data:'option='+option+'&feild=emp_id&value='+$('#employee_id').val(),
         type: "POST",
         success:function(data){
            console.log(data);
            $("#"+id).html(data);
        },
        error:function (){}
    });
 }
</script>
<div class="page-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="cover-profile" style="position: relative;">
                <div class="m-b-25">
                    <img class="m-b-25 img-fluid" src="assets/images/user-profile/bg-img1.jpg" alt="bg-img">
                    <div class="card-block user-info d-flex justify-content-center">
                        <div class="col-md-11">
                            <div class="media-left">
                                <a href="#" class="profile-image">

                                   <?php
                                   $dis_name = $_SESSION['employee']['display_name']!=""?$_SESSION['employee']['display_name']:$_SESSION['display_name'];
                                   if($_SESSION['employee']['image']!=""){ ?>
                                     <img src="<?php echo "uploads/all/".$_SESSION['employee']['image']; ?>" class=" img-120 user-img b-radius-10" alt="User">
                                 <?php }else{ ?>
                                  <span class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-1 text-center bg-white text-black  mt-2"><?php echo initials($dis_name); ?></span>
                              <?php } ?>

                          </a>
                      </div>
                      <div class="media-body row">
                        <div class="col-lg-12">
                            <div class="user-title">
                                <h4 class="text-white"><?php echo $_SESSION['display_name']; ?></h4>
                                <span class="text-white"><?php echo $_SESSION['user_email']; ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="float-right cover-btn">
                                <button type="button" class="btn btn-primary m-r-20 m-b-20"><i class="icofont icofont-plus"></i>
                                Follow</button>
                                <button type="button" class="btn btn-primary m-b-20" ><i class="icofont icofont-ui-messaging"></i>
                                Message</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="tab-header card">
            <ul class="nav  md-tabs infisuite_tab" id="mytab">
                <li class="nav-item p-10">
                    <a class="nav-link f-w-600 active" data-bs-toggle="tab" href="#personal" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Personal Info</a>
                </li>
                <li class="nav-item p-10">
                    <button type="button" class="nav-link f-w-600" data-bs-toggle="tab" location" role="tab" onclick="displayitemdata('COM_LIST_EMP_WORK_ORDER', 'display_side_work_order')">User Location</button>

                </li>
                <li class="nav-item p-10">
                    <a class="nav-link f-w-600" data-bs-toggle="tab" href="#bank" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">User Banks</a>

                </li>
                <li class="nav-item p-10">
                    <a class="nav-link f-w-600" data-bs-toggle="tab" href="#costcenter" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Cost Centre</a>
                </li>
                <li class="nav-item p-10">
                    <a class="nav-link f-w-600" data-bs-toggle="tab" href="#Passwordchange" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Password Change</a>
                </li>
                <li class="nav-item p-10">
                    <a class="nav-link f-w-600" data-bs-toggle="tab" href="#setting" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Setting</a>
                </li>
            </ul>
        </div>
        <div class="tab-content ">
            <div class="tab-pane active" id="personal" role="tabpanel">
                <div class="card">
                    <?php    $tabledata = toGetTableNameBaseApplicationPagesList('application_table_name','application_users',1);
                    $option_url = $tabledata['insert_page_name'];
                    $_GET['option'] =  $tabledata['insert_page_name'];
                    $_GET['Rid'] = deocdeId("id:".$_SESSION['userid']);
                    include_once "includes/controller.php";
                    $dispage = str_replace("views/", '', $disptemp);
                    include_once $dispage;
                    ?>

                </div>

            </div>

        </div>
        <div class="tab-pane" id="location" role="tabpanel">

            <?php

            $tabledata = toGetTableNameBaseApplicationPagesList('application_table_name','user_locations',1);
            $option_url = $tabledata['insert_page_name'];
            $_GET['option'] =  $tabledata['insert_page_name'];
            $_GET['Rid'] = deocdeId("id:".$_SESSION['userid']);
            include_once "includes/controller.php";
            $dispage = str_replace("views/", '', $disptemp);
            include_once $dispage;
            ?>
        </div>
    </div></div></div></div></div></div></div></div></div></div></div>
    <div class="tab-pane" id="bank" role="tabpanel">

            <?php

            $tabledata = toGetTableNameBaseApplicationPagesList('application_table_name','banks',1);
            $option_url = $tabledata['insert_page_name'];
            $_GET['option'] =  $tabledata['insert_page_name'];
            $_GET['Rid'] = deocdeId("id:".$_SESSION['userid']);
            include_once "includes/controller.php";
            $dispage = str_replace("views/", '', $disptemp);
            include_once $dispage;
            ?>
        </div>
    </div></div></div></div></div></div></div></div></div></div></div>

    <div class="tab-pane" id="costcenter" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Review</h5>
            </div>
            <div class="card-block">
                <div class="input-group">
                    <input aria-label="Right addon" type="text" class="form-control" placeholder="Right addon" />
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fas fa-envelope mt-1 mb-1"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="Passwordchange" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Review</h5>
            </div>
            <div class="card-block">
                <div class="input-group">
                    <input aria-label="Right addon" type="text" class="form-control" placeholder="Right addon" />
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fas fa-envelope mt-1 mb-1"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="Passwordchange_2" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">setting</h5>
            </div>
            <div class="card-block">
                <div class="input-group">
                    <input aria-label="Right addon" type="text" class="form-control" placeholder="Right addon" />
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fas fa-envelope mt-1 mb-1"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
