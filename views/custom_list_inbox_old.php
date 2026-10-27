<?php
$approval_url = API."?mode=toGetTransactionLogdata&status=".$_GET['status'];
$approval_data = callAPI($approval_url);
$approval_data = json_decode($approval_data,1);
$cate = $approval_data['cat_type'];
$all_count = $approval_data['all_count'];
$cat_count = $approval_data['cat_count'];
$employee = $approval_data['emp'];
$stausresult = ViewStatusData();
$type = $_GET['type'] !="" ? $_GET['type'] : "all";
$app_dis_data = $approval_data['cat_all'][$type];
$return['cat_count']=$count;
$status = ViewStatusData();
$_GET['status'] = $_GET['status']!=""?$_GET['status']:'99999';

?>
<script type="text/javascript">
    function changeloaod(row,type){
        var typ_url ="";
        if(type!=""){
            typ_url = "&type="+type;
        }
        window.location.href="index.php?option=COM_LIST_INBOX"+typ_url+"&row="+row;
    }
    function reloadPage(){
        location.reload(true);
    }
    function chanagestatus(status){

        if(status==99999){
            window.location.href="index.php?option=COM_LIST_INBOX";
        }else{
        window.location.href="index.php?option=COM_LIST_INBOX&status="+status;
        }
    }

</script>

<div class="main-body">
                    <div class="page-wrapper">

                      <!-- Page-body start -->
                      <div class="page-body">
                        <div class="card">
                          <!-- Email-card start -->
                          <div class="card-block email-card">

                            <div class="row j-pro j-content">
                              <!-- Left-side section start -->
                              <div class="col-lg-12 col-xl-3">
                                <div class="user-body bg-color-white p-2">
                                  <div class="p-20 text-center">

                                  </div>
                                  <ul class="page-list nav nav-tabs flex-column" id="pills-tab">
                                    <li class="nav-item mail-section">
                                      <a class="nav-link waves-effect d-block <?php if($type == "all"){ echo "active";
} ?>" data-toggle="pill" href="index.php?option=COM_LIST_INBOX" role="tab">
                                        <i class="icofont icofont-inbox"></i> Inbox
                                        <?php if(count((array)$all_count)>0){ ?>
                                        <span class="label label-primary float-end"><?php echo count((array)$all_count); ?></span>
                                    <?php } ?>
                                      </a>
                                    </li>


    <?php foreach ($cate as $cat) { ?>
                                    <li class="nav-item mail-section">
                                      <a href="index.php?option=COM_LIST_INBOX&type=<?php echo $cat; ?>" class="nav-link waves-effect d-block <?php if($type == $cat){ echo "active";
} ?>" data-toggle="pill" href="#e-starred" role="tab">
                                       <i class="icofont icofont-paper-plane"></i><?php echo viewTitle($cat); ?>
                                     <?php if(count((array)$cat_count[$cat])>0){ ?>
                                     <span class="label label-primary float-end"><?php echo count((array)$cat_count[$cat]); ?></span>
                                   <?php } ?>
                                      </a>
                                    </li>
                                   <?php } ?>

                                </ul>
                              </div>
                              <!-- Left-side section end -->
                              <!-- Right-side section start -->
                              <div class="col-lg-12 col-xl-9 ">

  <?php if($_GET['row']==""){ ?>

                                <div class="tab-content  bg-color-white p-2" id="pills-tabContent">
                                  <div class="tab-pane fade show active" id="e-inbox" role="tabpanel">

                                    <div class="mail-body">
                                      <div class="mail-body-header">
                                        <button type="button" class="btn btn-primary btn-xs waves-effect waves-light">
                                          <i class="ti-arrow-left"></i>
                                        </button>


                          <button type="button" class="btn btn-success btn-xs waves-effect waves-light" dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                           <i class="ti-menu-alt"></i>
                                        </button>
                                         <div class="dropdown-menu">
                                            <a href="index.php?option=COM_LIST_INBOX&type=<?php echo $_GET['type']; ?>"  class="dropdown-item waves-effect waves-light <?php echo !isset($_GET['sort'])?"Active":""; ?>" >All</a>
                                            <a  href="index.php?option=COM_LIST_INBOX&type=<?php echo $_GET['type']; ?>&sort=read" class="dropdown-item waves-effect waves-light <?php echo $_GET['sort']=="read"?"Active":""; ?>" >Read</a>
                                            <a href="index.php?option=COM_LIST_INBOX&type=<?php echo $_GET['type']; ?>&sort=unread" class="dropdown-item waves-effect waves-light <?php echo $_GET['sort']=="unread"?"Active":""; ?>" >Unread</a>
                                          </div>

                                        <button type="button" onclick="reloadPage();" class="btn btn-danger btn-xs waves-effect waves-light">
                                          <i class="ti-reload"></i>
                                        </button>

                                        <div class="btn-group dropdown-split-primary fr">
                                          <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                           Status
                                          </button>
                                          <div class="dropdown-menu">
                                            <a class="dropdown-item waves-effect waves-light <?php echo !isset($_GET['sort'])?"Active":""; ?>" href="index.php?option=COM_LIST_INBOX&type=<?php echo $_GET['type']; ?>">Newest</a>
                                            <a class="dropdown-item waves-effect waves-light <?php echo $_GET['sort']=="older"?"Active":""; ?>" href="index.php?option=COM_LIST_INBOX&type=<?php echo $_GET['type']; ?>&sort=older">Olders</a>
                                            <a class="dropdown-item waves-effect waves-light <?php echo $_GET['sort']=="unread"?"Active":""; ?>" href="index.php?option=COM_LIST_INBOX&type=<?php echo $_GET['type']; ?>&sort=unread">Unread</a>
                                          </div>
                                        </div>

                                      </div>
                                      <div class="mail-body-content">
                                        <div class="table-responsive">
                                          <table class="table">
                                              <?php   if($_GET['sort'] == 'older'){ ksort($app_dis_data);
}
              foreach ($app_dis_data as  $value) {
                $disname = $employee[$value['data']['created_by']]['display_name'];
                $disname = ($disname !="") ? $disname : $_SESSION['users'][$value['data']['created_by']];
                $disimage =  $employee[$value['data']['created_by']]['image'];
                $class_name = ""; $dis_class="d-flex";

                $class_name =  ($value['response_type'] != 0) ? "text-muted" : "";
                if(date('Y-m-d') == date('Y-m-d',strtotime($value['request_date']))){

                  $date_ime = date('h:i A',strtotime($value['request_date']));
                }else{
                  $date_ime = date('d M Y',strtotime($value['request_date']));
                }
                if($_GET['sort']!=''){
                  if($_GET['sort'] == 'unread' && $value['response_type'] != 0 ){
                    $dis_class = " d-none";
                  }
                  if($_GET['sort'] == 'read' && $value['response_type'] == 0 ){
                    $dis_class = " d-none";
                  }
                }

                ?>

        <tr class="unread" onclick="changeloaod(<?php echo $value['id'].",'".$_GET['type']."'" ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
          <th scope="col">
            <div class="check-star">
             <?php if($disimage != ""){ ?>
         <div class=" m-r-15 text-center userimageround">
            <img src="uploads/all/<?php echo $disimage; ?>" alt="Details" class="img-radius img-50">
         </div>
         <?php }else{ ?>
         <div class="img-radius img-50 align-top m-r-15 text-center userimageround">
            <span class="font-size-h3 font-weight-boldest"><?php echo initials($disname); ?></span>
         </div>
         <?php } ?>
            </div>
          </th>
          <th scope="col"><a  class="email-name waves-effect"><?php echo $disname; ?></a></br>
          <?php if($value['response_type'] != 0){ ?>
                        <span class="form-label label label-md label-primary mt-2" style="color: <?php echo "#".$stausresult[$value['data']['current_status']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$value['data']['current_status']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$value['data']['current_status']]['background_color']); ?>;"><?php echo $stausresult[$value['data']['current_status']]['name']; ?> </span>
                      <?php }else{ ?>
                        <span class="form-label label label-md label-primary mt-2">Waiting For Approval</span>
                      <?php } ?>
  </th>
          <th scope="col"><p><?php echo viewTitle($value['transaction_table']); ?> &nbsp; <?php echo viewTitle($value['transaction_id']); ?>
<?php if($value['response_type'] != 0 && $value['comments']!=""){ ?></br>
                   (<?php echo viewTitle($value['comments']); ?>)
                      <?php } ?>
</p>
          </th>
          <th scope="col">
          <td class="email-time"><?php echo $date_ime; ?></th>
        </tr>
<?php } ?>
                                          </table>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                </div>

<?php }else{
      $row_data = $approval_data['cat_all']['all'][$_GET['row']];
      ?>

        <?php
        $tabledata = toGetTableNameBaseApplicationPagesList('application_table_name',$row_data['transaction_table'],1);
        $option_url = $tabledata['insert_page_name'];
        if($tabledata['insert_page_name'] == 'COM_INSERT_LEAVE_REQUEST'){
            $tabledata['insert_page_name'] = "COM_INSERT_APPROVAL_LEAVE";
        }
        $_GET['option'] =  $tabledata['insert_page_name'];
        $_GET['Rid'] = deocdeId("id:".$row_data['transaction_id']);
        include_once "includes/controller.php";
        $dispage = str_replace("views/", '', $disptemp);
        include_once $dispage;
        ?>

    <?php } ?>

                              </div>
                              <!-- Right-side section end -->
                            </div>
                          </div>
                          <!-- Email-card end -->
                        </div>
                      </div>
                      <!-- Page-body start -->
                    </div>
                  </div>
