<?php
if($_GET['Rid'] !=""){
    include_once 'views/custom_insert_petty_cash_form.php';
    ?>
<input type="hidden" id="petty_cash_box_id" value="<?php echo $petty_cash_box_id; ?>">
    <?php
}else{
    $url1 = API."?mode=getpettycash&admin_type=".$_SESSION['admin_type'];
    $resultapi = callAPI($url1);
    $result = json_decode($resultapi,1);
    $petty_cash_box_owner = $result['petty_cash_box_owner'];
    $topup_frequency = $result['topup_frequency'];
    $im = 1;
    $petty_cash_box_id = "";
    foreach($petty_cash_box_owner as $pcbo){
        if($im ==1){
            $petty_cash_box_id = $pcbo['pc_box_id'];
        }
    }
?>
<script type="text/javascript">

    function getPettyCashBox(petty_cash_box_id){
        $("#petty_cash_box_id").val(petty_cash_box_id);
        getPettyCashBoxTab('list');
    }
    function getPettyCashBoxTab(type){
        var petty_cash_box_id = $("#petty_cash_box_id").val();
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#ajaxcallpage").html(loader);
        if(type == "add"){
            jQuery.ajax({
                url: "views/custom_insert_petty_cash_form.php",
                data:'type='+type+'&petty_cash_box_id='+petty_cash_box_id,
                type: "POST",
                success:function(data){
                    $("#ajaxcallpage").html(data);
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    $( ".datepicker" ).datepicker({
                        showOn: "both",
                        buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                        dateFormat: date_format,
                        minDate: -10,
                        maxDate: "+30D",
                        setDate: new Date(),
                        changeMonth: true,
                        changeYear: true,
                        //     }
                    });
                    console.log(data);
                },
                error:function (){}
            });
        }else{
            jQuery.ajax({
                url: "views/custom_insert_petty_cash_table.php",
                data:'type='+type+'&petty_cash_box_id='+petty_cash_box_id,
                type: "POST",
                success:function(data){
                    $("#ajaxcallpage").html(data);
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    console.log(data);
                },
                error:function (){}
            });
        }
    }
</script>
<script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    getPettyCashBox('<?php echo $petty_cash_box_id; ?>');
                }
            }
        </script>
<div class="row">
    <div class=" courier">
        <ul class="nav nav-tabs tabs row">
        <?php $im = 1; foreach($petty_cash_box_owner as $pcbo){
            if($im ==1){
                $petty_cash_box_id = $pcbo['pc_box_id'];
               }
            ?>
            <li class="nav-item col-lg-3 col-4" >
                <a class="nav-link position-relative p-3 <?php echo $im == 1?"active":""; ?>" data-bs-toggle="tab" href="#courier" role="tab" onclick="getPettyCashBox('<?php echo $pcbo['pc_box_id']; ?>')" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                   <i class="ti-package f-30 text-info p-l-25"></i>
                    <div class="row mt-1 text-dark">
                        <p class="f-w-700 m-b-0 col-lg-5 f-12">Description</p>
                        <p class="m-b-0 f-12 col-lg-7"><?php echo $pcbo['pc_box_description']; ?></p>
                    </div>
                    <div class="row mt-1 text-dark">
                        <p class="f-w-700 m-b-0 col-lg-5 f-12">Limit</p>
                        <p class="m-b-0 f-12 col-lg-7"><?php echo customeummarmonyformateifine($pcbo['pc_limit']); ?></p>
                    </div>
                    <div class="row mt-1 text-dark">
                        <p class="f-w-700 m-b-0 col-lg-5 f-12">TopUp Frequency</p>
                        <p class="m-b-0 f-12 col-lg-7"><?php echo $topup_frequency[$pcbo['topup_frequency']]; ?></p>
                    </div>
                    <div class="row mt-1 text-dark">
                        <p class="f-w-700 m-b-0 col-lg-5 f-12">TopUp Amount</p>
                        <p class="m-b-0 f-12 col-lg-7"><?php echo customeummarmonyformateifine($pcbo['topup_amount']); ?></p>
                    </div>
                </a>
            </li>
        <?php $im++;
} ?>
        </ul>
    </div>
</div>
<input type="hidden" id="petty_cash_box_id_2" value="<?php echo $petty_cash_box_id; ?>">
<div class="card mt-3">
    <ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3 float-right">
        <li class="nav-item">
            <button type="button" class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" list" role="tab" aria-selected="true" onclick="getPettyCashBoxTab('list')"> List </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link f-w-600 f-12" data-bs-toggle="tab" createxpence" role="tab" aria-selected="true" onclick="getPettyCashBoxTab('add')"> Add Expenses </button>
        </li>
    </ul>
    <div class="card-body" id="ajaxcallpage">

    </div>
</div>
<?php } ?>
