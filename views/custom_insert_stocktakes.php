<?php
 $_SESSION['apply_js']['multiselest'] = "1";
    if($_POST['submit']=="Add"){
        $_POST['company_id']=$_SESSION['company_id'];
        $_POST['user_id']=$_SESSION['userid'];
        $url = API."?mode=insertstocktakes&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        $response = json_decode($response,1);
        if($response['status'] == 'fail'){
            if(isset($_SESSION['error'][$response['error_no']])){
                $dis_err = explode(",", $response['other']);
                foreach ($dis_err as $va){
                    $dis_er[] = $dis_labels[$va];
                }
                $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
            }else{
                $error_message = $response['message'];
            }
        }else{
            if($_POST['Rid'] ==""){
                 header('Location:index.php?option=COM_VIEW_STOCKTAKES&Rid='.deocdeId("id:".$response['LASTID']));
             }
        }

    }
         $rid =  encodeId($_GET['Rid']);
        $rid = urldecode($rid);
        $rid = explode(":", $rid);
        $rid= $rid[1];
           $url = API."?mode=getstocktakes&Rid=".$rid;
        $data_details = callAPI($url);
        $details = json_decode($data_details,1);
        $stocktakes = $details['stocktakes'];
        $item_list = $details['item'];
        $location_nameres = $details['locations'];
        $users = $details['users'];
        if($stocktakes['status']!="0" && isset($stocktakes['status'])){
            header("location:index.php?option=COM_VIEW_STOCKTAKES&Rid=".$_GET['Rid']);
        }
?>
<script type="text/javascript">
    function selectItemss(){
        var selectItems = $(".select-items").val();
        jQuery.ajax({
        url: "ajax.php",
        data:'page=getstocktakelocations&selectitems='+selectItems,
        type: "POST",
        success:function(data){
             console.log(data);
             $("#item_location").html(data);
        },
        error:function (){}
    });

    }
</script>

<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid" >
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header mb-3">
                        <h5> Insert Stocktakes</h5>
                        <div class="card-header-right">
                            <?php if(isset($_GET['Rid'])){ ?>
                            <a href="index.php?option=COM_VIEW_STOCKTAKES&Rid=<?php echo $_GET['Rid']; ?>" class="btn btn-success"> <i class="fa fa-eye"></i> Priview</a>
                        <?php } ?>
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="kt-form"  method="post" action="" autocomplete="off" enctype="multipart/form-data">

                            <input type="hidden" name="Rid" value="<?php echo $rid; ?>">
                            <div class="form-group row ml-20 mr-20">
                                <div class="form-group col-lg-6">
                                    <span>User Name<span class="requried">*</span></span></br>
                                    <select aria-label="User"  class="form-select js-example-basic-single " name="user" required>
                                        <option value="">Select Any One</option>
                                <?php foreach ($users as $user) { ?>
                                    <option <?php if($user['id'] == $stocktakes['user_id']){ ?> selected <?php
} ?> value="<?php echo $user['id']; ?>"><?php echo ucwords($user['display_name']); ?></option>
                                <?php } ?>
                                </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <span>Name<span class="requried">*</span></span>
                                    <input aria-label="Name" class="form-control"  type="text"  id="name" name="name"  value="<?php echo $stocktakes['name']; ?>" required>
                                </div>
                                <div class="form-group col-lg-6">
                                    <span>Items<span class="requried">*</span></span></br>
                                    <select aria-label="Item[]"  class="form-select js-example-basic-multiple select-items" name="item[]"  style="width:100%" onchange="selectItemss()" multiple>
                                <?php $decitem= json_decode($stocktakes['item'],1); foreach ($item_list as $item) { ?>
                                    <option <?php if(in_array($item['id'], (array)$decitem)){?> selected <?php
} ?> value="<?php echo $item['id']; ?>"><?php echo ucwords($item['item_name']); ?></option>
                                <?php } ?>
                                </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <span>Planned Date<span class="requried">*</span></span>
                                    <input aria-label="Planned Date" class="form-control datepicker"  type="text"  id="Planned_date" name="Planned_date"  value="<?php echo $stocktakes['Planned_date']; ?>" required>
                                </div>
                                <div class="form-group col-lg-12">
                                    <span>Products Attributes<span class="requried">*</span></span>
                                    <input aria-label="Item Attribute" class="form-control date-picker"  type="text"  id="item_attribute" name="item_attribute"  value="" readonly>
                                </div>
                                <div class="form-group col-lg-6"></br>
                                    <span>Location<span class="requried">*</span></span></br>
                                    <select aria-label="Location[]"  class="form-select js-example-basic-multiple" id="item_location" name="location[]" style="width:100%" multiple>
                                <?php  $declocation= json_decode($stocktakes['location'],1);  foreach ($location_nameres as $location) { ?>
                                    <option <?php if(in_array($location['id'], (array)$declocation)){ ?> selected <?php
} ?> value="<?php echo $location['id']; ?>"><?php echo ucwords($location['location_name']); ?></option>
                                <?php } ?>
                                </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <div class="checkbox-fade fade-in-primary m-t-50">
                                      <label class="form-label">
                                        <input aria-label="Empty Location" type="checkbox" value="0" name="empty_location" <?php echo $stocktakes['empty_location']=="0"?"checked":""; ?>>
                                        <span class="cr">
                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                        <span>Include Empty location In Result</span>
                                      </label>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-9">
                                        <input type="hidden" name="submit" value="Add" />

                                        <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>

                                    </div>

                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
