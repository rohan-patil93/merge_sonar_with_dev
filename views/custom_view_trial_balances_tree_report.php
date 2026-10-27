<?php $_POST['balance_date'] = fromDate(); ?>
<script type="text/javascript">
    function getAccountDetail(account_code=""){
        console.log('--------------'+account_code);
        var balance_date = $("#balance_date").val();
        $("#balancedate_text").text(balance_date);
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#getaccountdetailid").html(loader);
        $.ajax({
            url: "views/custom_view_trial_balances_tree_report_ajax.php",
            data:'account_code='+account_code+'&balance_date='+balance_date,
            type: "POST",
            success:function(data){
                $("#getaccountdetailid").html(data);
            },
            error:function (){}
        });
    }
    function getAccountTransactionsDetail(account_code){
        var balance_date = $("#balance_date").val();
        var url  = "index.php?option=COM_VIEW_GENERAL_LEDGER&account_code="+account_code;

        window.open(url, '_blank');
    }
</script>
<script type="text/javascript">
        </script>
<div class="card ">
    <div class="card-block">
        <div class="row">
            <div class="col-lg-3">
                <div class="col bg-light-primary ">
                    <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                        <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                    </span>
                    <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo ucwords($_SESSION['config']['name']); ?></span>
                </div>
            </div>
            <div class="col-lg-6">
                <h4 class="card-label text-center f-w-700">Trial Balance Tree (<span id="balancedate_text"><?php echo fromDate($_POST['balance_date']); ?></span>) <small class="f-10 d-block">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small></h4>
            </div>
            <div class="col-lg-3">
                    <input aria-label="Balance Date" type="text" name="balance_date" id="balance_date" value="<?php echo fromDate($_POST['balance_date']); ?>" onchange="getAccountDetail()" class="form-control form-control-sm datepicker" style="font-size: 18px;height: 48px !important;padding: 10px 15px;">
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body" id="getaccountdetailid">
<?php

    include_once 'views/custom_view_trial_balances_tree_report_ajax.php';

?>
    </div>
</div>

