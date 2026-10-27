<?php
$_SESSION['apply_js']['multiselest'] = "1";
$_SESSION['apply_js']['Switch'] = '1';
if(isset($_POST['submit'])){
    $fr_id = '';
        $data['report_name'] = $_POST['report_name'];
        $data['description'] = $_POST['description'];
    if($_POST['fr_id']==""){
        $data['status_date'] = nowDate();
        $data['from_date'] = date("Y-m-d");
        $data['to_date'] = date('Y-m-d', strtotime('+5 years'));
        $data['created_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'];
        $data['company_id'] = $_SESSION['company_id'];
        $url = API."?mode=adddata&table=finance_reports&method=post";
        $response = file_post_contents($url, $data, 'insert');
        $rest = json_decode($response,1);
        $fr_id = $rest['LASTID'];
    }else{
        $data['updated_by'] = $_SESSION['userid'];
        $data['updated_date'] = nowDate();
        $url = API."?mode=updaterowtabledata&table=finance_reports&rid=".$_POST['fr_id']."&method=post";
        $response = file_post_contents($url, $data, 'update');
        $rest = json_decode($response,1);
        $fr_id = $_POST['fr_id'];
   }

}


$getrid =  encodeId($_GET['Rid']);
$decodrid = urldecode($getrid);
$spitrid = explode(':', $decodrid);
$fr_id = $spitrid['1'];
$vurl = API."?mode=getfinanacereportgroups&fr_id=".$fr_id;
$resultapi = callAPI($vurl);
$result = json_decode($resultapi,1);
$chart_of_accounts =$result['chart_of_accounts'];
$finance_reports =$result['finance_reports'];
$finance_report_groups =$result['finance_report_groups'];
$chartofaccounts = array();
foreach($chart_of_accounts as $chartofacc){
    $chartofaccounts[$chartofacc['id']] = $chartofacc;
}
foreach($finance_report_groups as $finanacerepogrp){
    $financereportgroupsreport[$finanacerepogrp['parent_group_id']][$finanacerepogrp['id']] = $finanacerepogrp;
    $financereportgroups[$finanacerepogrp['id']] = $finanacerepogrp;
}
?>
<script type="text/javascript">
    function addFinaanceAccGrp(){
        var postarray = $("#financereportgrpfrom").serialize();
        var fr_id = $("#fr_id").val();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=insertfinanacereportgroups&fr_id='+fr_id+'&'+postarray,
            success: function(response) {
                console.log(response);
                var respon = JSON.parse(response);
                console.log(respon.res);
                var result = respon.res;
                if(result.status =="fail"){
                   tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',result.message,'');
                }else{
                    if(result.LASTID == '0'){
                         GetViewReport();
                         editFinanaceGrp(0);
                       tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Record Updated Successfully','');
                    }else{
                         GetViewReport();
                         editFinanaceGrp(0);
                      tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Record Inserted Successfully','');
                    }

                }
            }
        });
    }

    function editFinanaceGrp(id="",pargrp=""){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#insertform").html(loader);
        var fr_id = $("#fr_id").val();
        $.ajax({
            url: "views/custom_insert_finance_report_groups_ajax.php",
            type: "POST",
            data: 'id='+id+'&pargrp='+pargrp+'&fr_id='+fr_id,
            success: function(response){
                $("#insertform").html(response);
                $('html, body').animate({
                    scrollTop: $("#insertform").offset().top
                }, 20);
                $(".js-example-basic-single").select2();
                $(".js-example-basic-multiple").select2();
                if($(".js-switch").length  > 0){
                    var elem = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                    elem.forEach(function(html) {
                        var switchery = new Switchery(html, { color: '#4099ff', jackColor: '#fff',size: 'medium' });
                    });
                }
            }
        });

    }
    function GetViewReport(){
        var loader = '<div class="text-center" id="loader-show_2"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#viewtable").html(loader);
        var fr_id = $("#fr_id").val();
        $.ajax({
            url: "views/custom_insert_finance_report_groups_table.php",
            type: "POST",
            data: 'pageshow=Y&fr_id='+fr_id,
            success: function(response){
                $("#viewtable").html(response);
            }
        });
    }
    function showChartAccount(){
        $(".custom-popup-slide").show('slow');
    }
    function closedPopup(){
        $(".custom-popup-slide").hide('slow');
    }
    function selectChartOfAccont(){
        var chart_of_account_codes = $("#chart_of_account_codes").val();
        var Rid = $("#Rid").val();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=selectchartofaccont&chart_of_account_codes='+chart_of_account_codes+'&Rid='+Rid,
            success: function(response){
                console.log(response)
                $("#chartofaccountmsg").html(response);
                showChartAccount();
            }
        });
    }
</script>

     <div class="card">
        <div class="card-header">
            <h5>Finance Reports</h5>
            <div class="card-header-right"></div>
        </div>
        <form method="POST">
        <div class="card-body">
            <div class="row">
            <div class="form-group col-lg-4">
                <span class="form-label">Report Name <span class="text-danger">*</span></span>
                <input aria-label="Report Name" type="text"  class="form-control text-capitalize"  name="report_name" value="<?php echo $finance_reports['report_name']; ?>" />
            </div>
            <div class="form-group col-lg-4">
                <span class="form-label">Description<span class="text-danger">*</span></span>
                <textarea aria-label="Description" type="text"  class="form-control text-capitalize"  name="description"  ><?php echo $finance_reports['description']; ?></textarea>
            </div>
            <div class="form-group col-lg-2">
                <input type="hidden" name="fr_id" id="fr_id" value="<?php echo $fr_id; ?>">
                <button  type="submit" name="submit" class="btn btn-primary btn-shadow-hover font-weight-bold m-t-30" > <?php echo $fr_id !=""?"Update":"Submit";?> </button>
            </div>
        </div>
        </div>
        </form>
    </div>
        <?php if($fr_id !=""){ ?>
    <div class="row mt-4">
        <div class="col-lg-12">
                  <div class="row">
                    <div class="col-lg-4" id="insertform">
                        <?php include_once 'views/custom_insert_finance_report_groups_ajax.php';?>
                    </div>
                    <div class="col-lg-8 card" id="viewtable">
                    <?php include_once 'views/custom_insert_finance_report_groups_table.php';?>
                </div>
            </div>

</div>
</div>
<?php } ?>
</div>
