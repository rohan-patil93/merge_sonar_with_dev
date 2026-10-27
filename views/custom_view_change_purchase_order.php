<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['save']) && $_POST['save'] == 'SAVE'){
    $url = API."?mode=toupdatepurchaseordertransaction&method=post";
    $response = file_post_contents($url, $_POST, 'update');
    $data1 = json_decode($response,1);
    header("location:index.php?option=COM_VIEW_CHANGE_PURCHASE_ORDER&ERR=3001");
}

$vdata1["where"] = array('is_active'=>'0');
$vdata1["field"] = "id, transaction_number";
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=vendor_rate_cards&data=".$vdata;
$vdatas = callAPI($vurl);
$data = json_decode($vdatas,1);

foreach($data as $dd){
    $val[$dd['id']] = $dd['transaction_number'];
}

?>
<script type="text/javascript">
    function selecttrnno(val){
        console.log(val);
        window.location.href = "index.php?option=COM_VIEW_CHANGE_PURCHASE_ORDER&RID="+val;
    }
</script>
<?php if($_GET['ERR'] !=""){ ?>
 <div class="alert alert-custom alert-success shadow-lg fade show mb-5 ">
    <strong class="f-14"><?php echo $_SESSION['error_message'][$_GET['ERR']]; ?></strong>
    <button type="button" class="btn-close float-right" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php } ?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="card-label">&nbsp; Transation Number Update </h5>
                </div>
                <div class="card-header-right table-card-header ">
                   <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                     <i class="icofont icofont-reply"></i>  Back        </a>
                 </div>
             </div>
             <div class="card-body">
                <form method="post" action="">
                <div class="form-group row col-lg-12">
                    <span class="col-lg-2 col-form-label text-lg-right">Transaction Number</span>

                    <div class="col-lg-3">
                        <select aria-label="Transaction" class="form-select js-example-basic-single" onchange="selecttrnno(this.value)" name="transaction_id" id="transaction_id">
                            <option value="0" > Select Any one </option>
                            <?php foreach($data as $dd){ ?>
                                <option value="<?php echo $dd['id']; ?>" <?php if($_GET['RID']==$dd['id']){ echo "selected";
} ?> ><?php echo $dd['transaction_number']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-7"></div>
                </div>
                <?php if(!empty($_GET['RID'])) { ?>
            <?php
              $_GET['table'] = 'vendor_rate_cards';
              $_GET['rid']   = $_GET['RID'];
              $_GET['title'] = 'Vendor Rate Cards';
              // IMPORTANT: the child renders inline-edit fields (txn no, item qty, ref total)
              include_once 'views/custom_qo_print_purchase_order.php';
            ?>

            <div class="mt-4 text-start">
              <button name="save" value="SAVE" class="btn btn-primary btn-sm px-5">
                <i class="fa fa-save me-2"></i> Save Changes
              </button>
            </div>
          <?php } ?>
      </form>
            </div>
        </div>
    </div>
</div>
