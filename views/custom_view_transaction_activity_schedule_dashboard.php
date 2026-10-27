<?php
$url = API."?mode=gettransactionactivityschedulesdashboartd";
$response = callAPI($url);
$result = json_decode($response,1);
$transactions = $result['transactions'];
?>
<script type="text/javascript">
    function getTransactionActivitySchedulesDashboard(transaction_table){
        $(".spanicon").html('');
        $("#spanicon_"+transaction_table).html('<i class="fa fa-check-circle text-warning f-16"></i>');
        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#htmltransactiondiv').html(htmloader);
        jQuery.ajax({
            url: "views/custom_view_transaction_activity_schedule_dashboard_ajax.php",
            data:'transaction_table='+transaction_table,
            type: "POST",
            success:function(data){
                $('#htmltransactiondiv').html(data);
            },
            error:function (){}
        });
    }
</script>
<ul class="dynamic_card_menu">
    <?php $i=0; foreach($transactions as $transname=>$transcnt){ ?>
    <li>
        <a href="#!" onclick="getTransactionActivitySchedulesDashboard('<?php echo $transname; ?>')">
            <div class="card ticket-card">
                <div class="card-body text-center p-2">
                    <p class="m-b-30 bg-c-blue lbl-card">
                    <?php echo ucwords(str_replace("_"," ",($transname))) ?> &nbsp;&nbsp; <span class="spanicon" id="spanicon_<?php echo $transname; ?>">
                        <?php if($i == 0){ ?>
                        <i class="fa fa-check-circle text-warning f-16"></i>
                    <?php } ?>
                    </span></p>

                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $transcnt; ?></h2>
                    </div>

                </div>
            </div>
        </a>
    </li>
    <?php $i++;
    } ?>
</ul>
<div id="htmltransactiondiv">
    <?php
        $_POST['transaction_table'] =array_key_first($transactions);
        include_once 'views/custom_view_transaction_activity_schedule_dashboard_ajax.php';
    ?>
</div>
