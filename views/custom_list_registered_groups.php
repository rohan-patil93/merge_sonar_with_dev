<?php
$url = API."?mode=getsubscriptionusers&list=Y&user_email=".$_SESSION['user_email'];
$subscripusers = callAPI($url);
$subscriptusers = json_decode($subscripusers,1);
$applicationuser = $subscriptusers['application_users'];
$domain_registrations = $subscriptusers['domain_registrations'];
if($_SESSION['insert_msg'] !=""){
    $msgcode = $_SESSION['insert_msg'];
    $_SESSION['insert_msg'] = "";
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
            }
        }
    </script>
<?php
}
$domainregistrations = array();
foreach($domain_registrations as $domreg){
    $domainregistrations[$domreg['user_email']] = $domreg['domain_name'];
}

?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky">
            <div class="card-header">
                <h5>Invite Users</h5>
                <div class="card-header-right">
                    <a  href="index.php?option=COM_LIST_CONFIG_PRINT_INVOICES"  class="btn btn-warning mb-2 desktop-view">
                        <i class="icofont icofont-list icon-white"> Print Config</i>
                    </a>
                    <a href="index.php?option=COM_LIST_ITEM_CATEGORIES"  class="btn btn-info mb-2 desktop-view">
                        <i class="icofont icofont-list icon-white"> Item Cat</i>
                    </a>
                    <a  href="index.php?option=COM_LIST_ITEMS"  class="btn btn-secondary mb-2 desktop-view">
                        <i class="icofont icofont-list icon-white"> Items</i>
                    </a>
                    <a  href="index.php?option=COM_LIST_HSN_AND_SAC_CODES"  class="btn btn-warning mb-2 desktop-view">
                        <i class="icofont icofont-list icon-white"> HSN Code</i>
                    </a>
                    <a  href="index.php?option=COM_LIST_SALES_INVOICES"  class="btn btn-primary mb-2 desktop-view">
                        <i class="icofont icofont-list icon-white"> Customer Inv</i>
                    </a>
                    <a href="index.php?option=COM_LIST_CUSTOMERS"  class="btn btn-info mb-2 desktop-view">
                        <i class="icofont icofont-list icon-white"> Customers</i>
                    </a>
                    <a  href="index.php?option=COM_INSERT_REGISTERED_GROUPS"  class="btn btn-success mb-2 desktop-view">
                        <i class="icofont icofont-plus icon-white"> Invite User</i>
                    </a>
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-block">
                <div class="card-body">

 <div class="row">
          <div class="col-lg-9"></div>
          <div class="col-lg-3">
                  <div class="input-group input-group-button mb-3">
                    <input aria-label="Search The Name" type="text" id="filter1111" class="form-control form-control-sm width1"  placeholder="Search The Name" data-bs-toggle="tooltip" data-placement="bottom" title="Search The Name">
                    <span class="input-group-text addon-style">
                        <span class=""><i class="ti-search"></i></span>
                    </span>
                </div>
          </div>
        </div>

                        <div class="row" id="results">
                            <?php
                            if(count((array)$applicationuser) !=0 ){
                            foreach($applicationuser as $applicationusers){
                                if($applicationusers['primary_email']!=""){
                            $deocdeId = deocdeId("id:".$applicationusers['id']); ?>
                            <div class="col-xl-4 col-md-12 results" >
                                <a href="index.php?option=COM_EDIT_REGISTERED_GROUPS&Rid=<?php echo $deocdeId;?>">
                                <div class="card proj-t-card p-3">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col p-l-0">
                                                <h6 class="mb-0 f-w-600"><?php
                                                    $exp_name = explode("_",$applicationusers['group_name']);
                                                echo $exp_name['0'];
                                            ?></h6>
                                                <p class="text-muted m-b-0 mt-2"><i class="ti-email"></i> <?php echo $applicationusers['primary_email']; ?></p>
                                                <p class="text-muted m-b-0 mt-2"><i class="ti-cloud"></i> <?php echo $domainregistrations[$applicationusers['primary_email']]; ?></p>

                                            </div>
                                        </div>
                                        <h6 class="pt-badge f-10" style="background-color: #ffe066; color:#FFFFFF">Active</h6>
                                    </div>
                                </div>
                            </a>
                            </div>
                        <?php }
}
}else{ ?>
                            <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Invititations </p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </div>
                        <?php } ?>

                        </div>

                </div>
            </div>
        </div>
    </div>
</div>
