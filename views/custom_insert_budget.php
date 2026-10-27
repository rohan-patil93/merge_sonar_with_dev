<?php
$budget_url =  API."?mode=togetbudgetuserdeailsaccount&fiscal_year=".$_GET['fiscal_year']."&cost_center=".$_GET['cost_center'];
$budget_data = callAPI($budget_url);
$budget_datas = json_decode($budget_data,1);
$cost_centers = $budget_datas['cost_centers'];
$budget_data = $budget_datas['main'];
$account_budgets = $budget_datas['account_budgets'];
$rbudget = $budget_datas['budget'];
if(count((array)$rbudget)>0){
    foreach($rbudget as $rbud){
        $rbd[$rbud['account_code']] = $rbud;
    }
}
$view_type = 'M';
if($account_budgets['budget_created']!=""){
    $view_type = $account_budgets['budget_created'];
}
if(isset($_POST['submit1'])){
    $post = $_POST;  $inveral = 'M';

    if($post['inveral']=='month'){ $inveral = 'M';
}
    if($post['inveral']=='half'){ $inveral = 'H';
}
    if($post['inveral']=='quater'){ $inveral = 'Q';
}
    if($post['inveral']=='yearly'){ $inveral = 'Y';
}
    $data['budget_name'] = $post['name'];
    $data['company_id'] = $_SESSION['company_id'];
    $data['budget_date'] = $post['now_date'];
    $data['budget_version'] = $post['budget_version'];
    $data['inveral'] = $inveral;
    $data['cost_center_id'] = $post['cost_center_id'];
    $data['fiscal_year'] = $post['fiscal_year'];
    $data['user_id'] = $post['user_id'];
    $data['action_if_exceed_yearly'] = 0;
    $data['action_if_exceed_quaterly'] = 0;
    $data['action_if_exceed_halfyearly'] = 0;
    $data['action_if_exceed_month'] = 0;
    if($post['inveral'] == 'month'){
        $data['action_if_exceed_month'] = 1;
        for($m=1; $m<$post['count']; $m++){
            $x=1; $y=1; $m_tatoal=0;
            for($n=1; $n<13;$n++){
                $field = "name_".$m."_".$n;
                $dat['account_code'] = $post['name_'.$m.'_account_code'];
                $dat['distribution_m'.$n] = $post[$field];
                $ccww = 'distribution_q'.$y;
                if(!isset($dat[$ccww])){ $dat[$ccww] = 0;
}
                $dat[$ccww] = ($post[$field]!="" ? $post[$field] : 0)+$dat[$ccww];
                if($x==3){ $x=0; $y++;
}
                if(0 < $n && $n < 7){ $dat['distribution_h1'] = $dat['distribution_h1']+($post[$field]!="" ? $post[$field] : 0);
}
                if(6<$n && $n<14){ $dat['distribution_h2'] = $dat['distribution_h2'] +  ($post[$field]!="" ? $post[$field] : 0);
}
                $x++;
            }
            $dat['distribution_tot'] = $post['name_'.$m.'_total'];
            $data['data'][] = $dat;
            $dat = array();
        }
    }
    if($post['inveral'] == 'quater'){
        $data['action_if_exceed_quaterly'] = 1;
        for($m=1; $m<$post['count']; $m++){
            $x=1; $y=1; $m_tatoal=0;
            for($n=1; $n<5;$n++){
                $field = "quater_".$m."_".$n;
                $dat['account_code'] = $post['quater_'.$m.'_account_code'];

                $dat['distribution_q'.$n] = $post[$field];

                if(0 < $n && $n < 3){
                    $dat['distribution_h1'] = $dat['distribution_h1']+($post[$field]!="" ? $post[$field] : 0);
                }
                if(2<$n && $n<5){
                    $dat['distribution_h2'] = $dat['distribution_h2'] +  ($post[$field]!="" ? $post[$field] : 0);
                }
            }
            $dat['distribution_tot'] = $post['quater_'.$m.'_total'];
            $data['data'][] = $dat;
            $dat = array();
        }
    }
    if($post['inveral'] == 'half'){
        $data['action_if_exceed_halfyearly'] = 1;
        for($m=1; $m<$post['count']; $m++){
            $x=1; $y=1; $m_tatoal=0;

            for($n=1; $n<3;$n++){
                $field = "half_".$m."_".$n;

                $dat['account_code'] = $post['half_'.$m.'_account_code'];
                $dat['distribution_h'.$n] = $post[$field];
                $ccww = 'distribution_h'.$n;
            }
            $dat['distribution_tot'] = $post['half_'.$m.'_total'];
            $data['data'][] = $dat;
            $dat = array();
        }
    }
    if($post['inveral'] == 'yearly'){
        $data['action_if_exceed_yearly'] = 1;
        for($m=1; $m<$post['count']; $m++){
            $dat['account_code'] = $post['year_'.$m.'_account_code'];
            $dat['distribution_tot'] = $post['year_'.$m.'_1'];
            $data['data'][] = $dat;
            $dat = array();
        }
    }
    ///echo "-----------------------***************************************--------------------------";
    $url =  API."?mode=budgetinsertaccount&method=post";
    $response = file_post_contents($url, $data, 'insert');
    if ($response) {
        header("location:index.php?option=COM_INSERT_BUDGET&fiscal_year=".$post['fiscal_year']."&cost_center=".$post['cost_center_id']);
    }
    $alltotal =0; $all_tt =array();
    $alltotal_qt =0; $all_tt_qt =array();
}

?>
<style type="text/css">
    pointer-events: none;
    background-color: #f0f0f0; /* Light grey background to indicate read-only */
}

    pointer-events: auto;
    background-color: #ffffff; /* White background to indicate editable */
}
</style>
<script type="text/javascript">
    function validaiontoall(){
        console.log('VVVVVVVVVV');
        var cost_center_id = $('#cost_center_id').val();
        var  fiscal_year = $('#fiscal_year').val();
        if(cost_center_id!=0){
            window.location.href = 'index.php?option=COM_INSERT_BUDGET&fiscal_year='+fiscal_year+'&cost_center='+cost_center_id;
        }else{
            $('#cost_center_id_err').show();
        }

    }
    function inveral123(val){
        var vals = "month,quater,half,yearly";
        var split_val = vals.split(",");
        for(var i=0; i<5; i++){
            if(split_val[i] == val){
                document.getElementById(split_val[i]).style.display = "block";
            }else{
                document.getElementById(split_val[i]).style.display = "none";
            }
        }
    }
    function totalcaluclationyear(id,val){
        var spilt_data =  id.split("_");
        var column_total = 0;
        var kt_count = $('#lkt_'+spilt_data[1]+'_total').val();
        var gt_count = $('#lkt_'+spilt_data[1]).val();
        column_total = parseFloat(kt_count) + parseFloat(val);
        $('#lkt_'+spilt_data[1]+'_total').val(column_total);
        $("#lkt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+spilt_data[1]).html(column_total);
        var gd_total = $('#total_lgdv').val();

        gd_total = parseFloat(gd_total) + parseFloat(val);
        $("#total_lgd").html(gd_total);
        $("#total_lgdv").val(gd_total);
    }
    function totalcaluclationhalfyaerly(id,val){
        var spilt_data =  id.split("_");
        var inveral = $('#inveral').val();
        counts = 2;

        var row_total = 0;
        for(i=1;i<counts;i++){
            var row_id = spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[3]+"_"+spilt_data[4]+"_"+i;
            var money = $('#'+row_id).val();
            if(money == ''){ money = 0;}
            row_total = parseFloat(row_total) + parseFloat(money);

        }
        $('#'+spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[3]+"_"+spilt_data[4]+"_total").val(row_total);
        var column_total = 0;
        var kt_count = $('#mkt_'+spilt_data[1]).val();
        for(var k=1; k < kt_count; k++){
            var cloumn_id = spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+k+"_"+spilt_data[4]+"_"+spilt_data[5];
            var cl_money = $('#'+cloumn_id).val();
            if(cl_money == ''){ cl_money = 0;}
            column_total = parseFloat(column_total) + parseFloat(cl_money);
        }
        $("#mkt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+spilt_data[5]).html(column_total);

        var kt_total = 0;
        for(var n=1; n<counts;n++){
            var cats_id = "#mkt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+n;
            console.log(cats_id);
            var k_total = $(cats_id).html();
            if(k_total == ''){ k_total = 0;}
            console.log(k_total);
            kt_total = parseFloat(kt_total) + parseFloat(k_total);
            console.log(kt_total);
        }
        $("#mcat_total_"+spilt_data[1]).html(kt_total);

        var gd_total = 0;
        var gd_count = $('#mgd_count').val();
        for (var m = 1; m < gd_count; m++) {
            var grand_id = "mkt_"+m+"_"+spilt_data[2]+"_"+m+"_"+spilt_data[4]+"_"+spilt_data[5];
            var gd_money = $('#'+grand_id).html();
            if(gd_money == ''){ gd_money = 0;}
            gd_total = parseFloat(gd_total) + parseFloat(gd_money);
        }
        $("#mgd_"+spilt_data[5]).html(gd_total);
        $("#mgdv_"+spilt_data[5]).val(gd_total);
        var total_gd = 0;
        for(var n=1; n<counts;n++){
            var gdv_total = $('#mgdv_'+n).val();
            if(gdv_total == ''){ gdv_total = 0;}
            console.log(gdv_total);
            total_gd = parseFloat(total_gd) + parseFloat(gdv_total);
            console.log(total_gd);
        }
        $("#total_mgd").html(total_gd);
        $("#total_mgdv").val(total_gdv);
    }
    function totalcaluclationquaterly(id,val){
        var spilt_data =  id.split("_");
        var inveral = $('#inveral').val();
        if(inveral == "quater"){
            counts = 5;
        }else{
            counts = 3;
        }

        var row_total = 0;
        for(i=1;i<counts;i++){
            var row_id = spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[3]+"_"+spilt_data[4]+"_"+i;
            var money = $('#'+row_id).val();
            if(money == ''){ money = 0;}
            row_total = parseFloat(row_total) + parseFloat(money);

            console.log("row_id"+row_id+"Check money:"+money+"row_total:"+row_total);
        }
        $('#'+spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[3]+"_"+spilt_data[4]+"_total").val(row_total);
        var column_total = 0;
        if(inveral == "quater"){
            var kt_count = $('#mkt_'+spilt_data[1]).val();
        }else{
            var kt_count = $('#ykt_'+spilt_data[1]).val();
        }
        for(var k=1; k < kt_count; k++){
            var cloumn_id = spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+k+"_"+spilt_data[4]+"_"+spilt_data[5];
            var cl_money = $('#'+cloumn_id).val();
            if(cl_money == ''){ cl_money = 0;}
            column_total = parseFloat(column_total) + parseFloat(cl_money);
        }
        if(inveral == "quater"){
        $("#mkt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+spilt_data[5]).html(column_total);
        }else{
        $("#ykt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+spilt_data[5]).html(column_total);
        }

        var kt_total = 0;
        for(var n=1; n<counts;n++){
            if(inveral == "quater"){
            var cats_id = "#mkt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+n;
            }else{
            var cats_id = "#ykt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+n;
            }
            console.log(cats_id);
            var k_total = $(cats_id).html();
            if(k_total == ''){ k_total = 0;}
            console.log(k_total);
            kt_total = parseFloat(kt_total) + parseFloat(k_total);
            console.log(kt_total);
        }
        if(inveral == "quater"){
            $("#mcat_total_"+spilt_data[1]).html(kt_total);
        }else{
            $("#ycat_total_"+spilt_data[1]).html(kt_total);
        }

        var gd_total = 0;
        var gd_count = $('#mgd_count').val();
        for (var m = 1; m < gd_count; m++) {
            if(inveral == "quater"){
            var grand_id = "mkt_"+m+"_"+spilt_data[2]+"_"+m+"_"+spilt_data[4]+"_"+spilt_data[5];
            }else{
            var grand_id = "ykt_"+m+"_"+spilt_data[2]+"_"+m+"_"+spilt_data[4]+"_"+spilt_data[5];
            }
            var gd_money = $('#'+grand_id).html();
            if(gd_money == ''){ gd_money = 0;}
            gd_total = parseFloat(gd_total) + parseFloat(gd_money);
        }
        if(inveral == "quater"){
            $("#mgd_"+spilt_data[5]).html(gd_total);
            $("#mgdv_"+spilt_data[5]).val(gd_total);
        }else{
            $("#ygd_"+spilt_data[5]).html(gd_total);
            $("#ygdv_"+spilt_data[5]).val(gd_total);
        }

        var total_gd = 0;
        for(var n=1; n<counts;n++){
            if(inveral == "quater"){
            var gdv_total = $('#mgdv_'+n).val();
            }else{
                var gdv_total = $('#ygdv_'+n).val();
            }
            if(gdv_total == ''){ gdv_total = 0;}
            console.log(gdv_total);
            total_gd = parseFloat(total_gd) + parseFloat(gdv_total);
            console.log(total_gd);
        }
        if(inveral == "quater"){
            $("#total_mgd").html(total_gd);
            $("#total_mgdv").val(total_gdv);
        }else{
            $("#total_ygd").html(total_gd);
            $("#total_ygdv").val(total_gdv);
        }

    }
    function totalcaluclation(id,val){
        var spilt_data =  id.split("_");
        var inveral = $('#inveral').val();
        counts = 13;

        var row_total = 0;
        for(i=1;i<counts;i++){
            var row_id = spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[3]+"_"+spilt_data[4]+"_"+i;
            var money = $('#'+row_id).val();
            if(money == ''){ money = 0;}
            row_total = parseFloat(row_total) + parseFloat(money);

        }
        $('#'+spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[3]+"_"+spilt_data[4]+"_total").val(row_total);
        var column_total = 0;
        var kt_count = $('#kt_'+spilt_data[1]).val();
        for(var k=1; k < kt_count; k++){
            var cloumn_id = spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+k+"_"+spilt_data[4]+"_"+spilt_data[5];
            var cl_money = $('#'+cloumn_id).val();
            if(cl_money == ''){ cl_money = 0;}
            column_total = parseFloat(column_total) + parseFloat(cl_money);
        }
        $("#kt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+spilt_data[5]).html(column_total);

        var kt_total = 0;
        for(var n=1; n<counts;n++){
            var cats_id = "#kt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+n;
            console.log(cats_id);
            var k_total = $(cats_id).html();
            if(k_total == ''){ k_total = 0;}
            console.log(k_total);
            kt_total = parseFloat(kt_total) + parseFloat(k_total);
            console.log(kt_total);
        }
        $("#cat_total_"+spilt_data[1]).html(kt_total);

        var gd_total = 0;
        var gd_count = $('#gd_count').val();
        for (var m = 1; m < gd_count; m++) {
            var grand_id = "kt_"+m+"_"+spilt_data[2]+"_"+m+"_"+spilt_data[4]+"_"+spilt_data[5];
            var gd_money = $('#'+grand_id).html();
            if(gd_money == ''){ gd_money = 0;}
            gd_total = parseFloat(gd_total) + parseFloat(gd_money);
        }
        $("#gd_"+spilt_data[5]).html(gd_total);
        $("#gdv_"+spilt_data[5]).val(gd_total);
        var total_gd = 0;
        for(var n=1; n<counts;n++){
            var gdv_total = $('#gdv_'+n).val();
            if(gdv_total == ''){ gdv_total = 0;}
            console.log(gdv_total);
            total_gd = parseFloat(total_gd) + parseFloat(gdv_total);
            console.log(total_gd);
        }
        $("#total_gd").html(total_gd);
        $("#total_gdv").val(total_gdv);
    }
</script>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <!--- Header Start --->
            <div class="card-header table-card-header">
                <h5><?php echo "Budget";  ?></h5>
                <div class="card-header-right table-card-header">

                <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                    &nbsp;<i class="ki ki-long-arrow-back icon-xs"></i>
                    Back
                </a>
                </div>
            </div>
            <!--- Header End --->
            <!--- Main Data Start --->
            <div class="card card-custom">
                <div class="card-body">

                    <form class="kt-form kt-form--label-right  p-4" name="myForm" id="myForm" action="" method="post" enctype="multipart/form-data"  >

                        <div class="budget-section row">
                            <div class="input-field col-lg-2">
                                <span>Name </span>
                                <input aria-label="Name" type="text" name="name" class="form-control" value="<?php echo $account_budgets['budget_name']; ?>">
                            </div>

                            <div class="input-field input-field-pre col-lg-2">
                                <span>Financial Year</span>
                                <select aria-label="Fiscal Year" class="form-select js-example-basic-single" onchange="validaiontoall()" id="fiscal_year" name="fiscal_year">
                                    <?php $year = date('Y');
                                    $view_fiscal_year = $account_budgets['fiscal_year']!=""? $account_budgets['fiscal_year'] : $_GET['fiscal_year'];
                                    for($i=1; $i<6; $i++) {
                                        $to_year = $year+1;
                                        $valse =  $year."-".$to_year;
                                    ?>
                                        <option value="<?php echo $valse; ?>" <?php if($valse ==$view_fiscal_year  ){ echo "selected";
} ?>  >
                                            <?php echo $year."-".$to_year; ?>
                                        </option>
                                    <?php $year = $year+1;
} ?>
                                </select>
                            </div>
                            <div class="input-field input-field-pre col-lg-2">
                                <span>Interval <?php //echo $view_type; ?></span>
                                <select aria-label="Inveral" class="form-select js-example-basic-single" name="inveral" onchange="inveral123(this.value)" id="inveral">
                                    <option <?php if($view_type == 'M' ){ echo "selected";
} ?> value="month">Monthly</option>
                                    <option <?php if($view_type == 'Q' ){ echo "selected";
} ?> value="quater">Quaterly</option>
                                    <option <?php if($view_type == 'H' ){ echo "selected";
} ?> value="half">Half Yearly</option>
                                    <option  <?php if($view_type == 'Y' ){ echo "selected";
} ?> value="yearly">Yearly</option>
                                </select>
                            </div>

                            <?php
                                $ver = $account_budgets['budget_version']!="" ? $account_budgets['budget_version'] : 1;
                                $nwever= $account_budgets['budget_version']!="" ? $account_budgets['budget_version']+1 : 1;
                            ?>
                            <div class="input-field input-field-ver col-lg-1">
                                <span>Version</span>
                                <input aria-label="Input field" type="text"  class="form-control" name="" readonly value="<?php echo $ver; ?>">
                            </div>
                            <input type="hidden" name="budget_version" value="<?php echo $nwever; ?>">

                            <div class="input-field input-field-pre col-lg-2" >
                                <span>Cost Center</span>
                                <select aria-label="Cost Center" class="form-select js-example-basic-single" onchange="validaiontoall()" id="cost_center_id" name="cost_center_id">
                                    <option value="0"> Select Any One</option>
                                    <?php
                                    $view_cost_center = $account_budgets['cost_center_id']!=""?$account_budgets['cost_center_id'] : $_GET['cost_center'];
                                    foreach($cost_centers as $cost_center){ ?>
                                        <option <?php if( $view_cost_center == $cost_center['id'] ){ echo "selected";
} ?> value="<?php echo $cost_center['id']; ?>"> <?php echo $cost_center['cost_center_name']; ?></option>
                                    <?php } ?>

                                </select>
                                 <?php if($_GET['cost_center']==""){ ?>
                                <div  class="text-danger f-14 f-w-600 mt-2"> Please Select The Cost Center </div>
                                <?php } ?>
                            </div>
                        </div>

                        <?php if(count((array)$budget_data)>0 && count((array)$cost_centers)>0 ){ ?>

                        <div class="budget-table m-t-20" <?php if($_GET['cost_center']==""){ ?> contenteditable="false" <?php
} ?>  id="month"
 <?php if($view_type!='M'){?> style="display: none;" <?php
} ?>>
                            <h3>Budget Monthly</h3>
                            <table>
                                <tr style="background-color: #22b9ff; height: 40px;">
                                    <th scope="col">Account</th>
                                    <th scope="col">APRIL</th>
                                    <th scope="col">MAY</th>
                                    <th scope="col">JUN</th>
                                    <th scope="col">JUL</th>
                                    <th scope="col">AUG</th>
                                    <th scope="col">SEP</th>
                                    <th scope="col">OCT</th>
                                    <th scope="col">NOV</th>
                                    <th scope="col">DEC</th>
                                    <th scope="col">JAN</th>
                                    <th scope="col">FEB</th>
                                    <th scope="col">MAR</th>
                                    <th scope="col">Total</th>
                                </tr>
                                <?php $k=1; $z=1;
                                foreach ($budget_data as $budget_details) { ?>
                                    <tr style="background-color: #bbb4b4; height: 40px;">
                                        <td style="padding-left: 5px;"><?php echo $budget_details['section_name'];?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?php
                                    foreach ($budget_details['data'] as $budgets) { ?>
                                        <tr style="background-color: #e8ca1f; height: 40px;">
                                             <td style="padding-left: 10px;">
                                                 <?php echo $budgets['group_name'];?>
                                             </td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                        </tr>

                                         <?php $l=1 ;
                                         $cat_item_lin = array();
                                         foreach ($budgets['details'] as $budget) {
                                             $value_id= "k_".$k."_r_".$l."_c_";
                                             $name= "name_".$z."_";
                                             for ($n=1; $n <13 ; $n++) {
                                                 $vvccff = "distribution_m".$n;
                                                 $cat_item_lin[$n] = $cat_item_lin[$n]+$rbd[$budget['account_code']][$vvccff];
                                             }
                                        ?>
                                     <tr>
                                         <td class="padlft" style="min-width: 200px;padding-left: 15px; text-align: left;"><?php echo $budget['account_name'];?>
                                         <input type="hidden" name="<?php echo $name.'account_code'; ?>" value="<?php echo $budget['account_code'];?>">
                                         </td>
                                         <td>
                                             <input aria-label="Input field" type="text" name="<?php echo $name.'1'; ?>"  value="<?php echo round($rbd[$budget['account_code']]['distribution_m1']); ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'1'; ?>" class="form-control text-right">
                                         </td>
                                         <td>
                                             <input aria-label="Input field" type="text" name="<?php echo $name.'2'; ?>" value="<?php echo round($rbd[$budget['account_code']]['distribution_m2']); ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'2'; ?>" class="form-control text-right">
                                         </td>
                                         <td>
                                             <input aria-label="Input field" type="text" name="<?php echo $name.'3'; ?>" value="<?php echo round($rbd[$budget['account_code']]['distribution_m3']); ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'3'; ?>" class="form-control text-right">
                                         </td>
                                         <td>
                                             <input aria-label="Input field" type="text" name="<?php echo $name.'4'; ?>" value="<?php echo round($rbd[$budget['account_code']]['distribution_m4']); ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'4'; ?>" class="form-control text-right">
                                         </td>
                                         <td>
                                             <input aria-label="Input field" type="text" name="<?php echo $name.'5'; ?>" value="<?php echo round($rbd[$budget['account_code']]['distribution_m5']); ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'5'; ?>" class="form-control text-right">
                                         </td>
                                         <td>
                                             <input aria-label="Input field" type="text" name="<?php echo $name.'6'; ?>" value="<?php echo round($rbd[$budget['account_code']]['distribution_m6']); ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'6'; ?>" class="form-control text-right">
                                         </td>
                                         <td>
                                             <input aria-label="Input field" type="text" name="<?php echo $name.'7'; ?>"
                                              value="<?php echo round($rbd[$budget['account_code']]['distribution_m7']); ?>"
                                              onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'7'; ?>" class="form-control text-right">
                                         </td>
                                         <td>
                                             <input aria-label="Input field" type="text" name="<?php echo $name.'8'; ?>" onblur="totalcaluclation(this.id,this.value)"  value="<?php echo round($rbd[$budget['account_code']]['distribution_m8']); ?>"  id="<?php echo $value_id.'8'; ?>" class="form-control text-right">
                                         </td>
                                         <td>
                                             <input aria-label="Input field" type="text" name="<?php echo $name.'9'; ?>"  value="<?php echo round($rbd[$budget['account_code']]['distribution_m9']); ?>"  onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'9'; ?>" class="form-control text-right">
                                         </td>
                                         <td>
                                             <input aria-label="Input field" type="text" name="<?php echo $name.'10'; ?>"  value="<?php echo round($rbd[$budget['account_code']]['distribution_m10']); ?>"  onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'10'; ?>" class="form-control text-right">
                                         </td>
                                         <td>
                                             <input aria-label="Input field" type="text" name="<?php echo $name.'11'; ?>"  value="<?php echo round($rbd[$budget['account_code']]['distribution_m11']); ?>"  onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'11'; ?>" class="form-control text-right">
                                         </td>
                                         <td>
                                             <input aria-label="Input field" type="text" name="<?php echo $name.'12'; ?>"  value="<?php echo round($rbd[$budget['account_code']]['distribution_m12']); ?>"  onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'12'; ?>" class="form-control text-right">
                                         </td>
                                         <td><input aria-label="Input field" type="text" readonly name="<?php echo $name.'total'; ?>" id="<?php echo $value_id.'total'; ?>"  value="<?php echo round($rbd[$budget['account_code']]['distribution_tot']); ?>"  class="form-control text-right"></td>
                                     </tr>
                                     <?php $l++; $z++;
} ?>
                                     <?php $cat_id = "kt_".$k."_r_".$k."_c_";
                                     $cat_idss = "kt_".$k;
                                     ?>
                                 <tr class="totalbudget" style="background-color: #d3d4d5; height: 40px;">
                                     <td style="padding-left: 10px;"> Total</td>

                                     <?php $line_total = 0;
                                         for($g =1 ; $g<13;$g++) { ?>
                                         <td class="text-right">
                                             <div id="<?php echo $cat_id.$g; ?>" style="padding-right: 5px;">
                                                 <?php
                                                     echo $cat_item_lin[$g];
                                                     $all_tt[$g] = $all_tt[$g]+$cat_item_lin[$g];
                                                     $line_total = $line_total+$cat_item_lin[$g];
                                                 ?>
                                             </div>
                                         </td>
                                     <?php }
                                         $alltotal = $alltotal+$line_total;
                                     ?>

                                     <td class="text-right"><div id="<?php echo "cat_total_".$k; ?>" style="padding-right: 5px;"><?php echo $line_total; ?></div></td>
                                 </tr>

                                 <input type="hidden" name="" id="<?php echo $cat_idss; ?>" value="<?php echo $l; ?>">
                                 <?php $k++; ?>
                                 <?php } ?>
                                 <?php } ?>
                                 <input type="hidden" name="" id="gd_count" value="<?php echo $k; ?>">
                                 <tr class="totalbudget" style="background-color: #88d98a; height: 40px;">
                                     <td style="padding-left: 10px;">Grand Total</td>
                                     <?php
                                     for($g =1 ; $g<13;$g++) { ?>
                                         <td class="text-right">
                                             <div id="<?php echo "gd_".$g; ?>" style="padding-right: 5px;">
                                             <?php echo $all_tt[$g]; ?>
                                             </div>
                                         </td>
                                         <input type="hidden" name="" id="<?php echo "gdv_".$g; ?>" value="">
                                     <?php } ?>
                                     <td id="total_gd" style="padding-right: 5px;" class="text-right">
                                         <?php echo $alltotal; ?>
                                     </td>
                                     <input type="hidden" name="" id="total_gdv" value="0">
                                 </tr>
                            </table>
                        </div>

                        <!--- Quater Section Start --->
                        <div class="budget-table m-t-20" id="quater" <?php if($view_type!='Q'){?> style="display: none;" <?php
} ?>>
                            <h3>Budget Quaterly</h3>
                            <table>
                                <tr style="background-color: #22b9ff; height: 40px;" >
                                    <th scope="col">Account</th>
                                    <th scope="col">Quater 1</th>
                                    <th scope="col">Quater 2</th>
                                    <th scope="col">Quater 3</th>
                                    <th scope="col">Quater 4</th>
                                    <th scope="col">Total</th>
                                </tr>
                                <?php $k=1;$z =1; foreach ($budget_data as $budget_details) { ?>
                                    <tr style="background-color: #bbb4b4; height: 40px;">
                                        <td style="padding-left: 5px;"><?php echo $budget_details['section_name'];?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?php
                                    foreach ($budget_details['data'] as $budgets) { ?>
                                        <tr style="background-color: #e8ca1f; height: 40px;" >
                                            <td style="padding-left: 10px;"><?php echo $budgets['group_name'];?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>

                                        </tr>
                                        <?php $l=1 ;
                                        $cat_item_lin_qu = array();
                                        foreach ($budgets['details'] as $budget) {
                                            $value_id= "mk_".$k."_r_".$l."_c_";
                                            $name= "quater_".$z."_";
                                            for ($n=1; $n <5 ; $n++) {
                                             $vvccff = "distribution_q".$n;
                                             $cat_item_lin_qu[$n] = $cat_item_lin_qu[$n]+$rbd[$budget['account_code']][$vvccff];
                                             }
                                        ?>
                                            <tr>
                                                <td class="padlft" style="padding-left: 15px; text-align: left;">    <?php echo $budget['account_name'];?>
                                                <input type="hidden" name="<?php echo $name.'account_code'; ?>" value="<?php echo $budget['account_code'];?>">
                                                </td>
                                                <td>
                                                    <input aria-label="Input field" type="text" value="<?php echo round($rbd[$budget['account_code']]['distribution_q1']); ?>" name="<?php echo $name.'1'; ?>"  onblur="totalcaluclationquaterly(this.id,this.value)" id="<?php echo $value_id.'1'; ?>" class="form-control text-right">
                                                </td>
                                                <td>
                                                    <input aria-label="Input field" type="text" value="<?php echo round($rbd[$budget['account_code']]['distribution_q2']); ?>" name="<?php echo $name.'2'; ?>"  onblur="totalcaluclationquaterly(this.id,this.value)" id="<?php echo $value_id.'2'; ?>" class="form-control text-right">
                                                </td>
                                                <td>
                                                    <input aria-label="Input field" type="text" value="<?php echo round($rbd[$budget['account_code']]['distribution_q3']); ?>" name="<?php echo $name.'3'; ?>"  onblur="totalcaluclationquaterly(this.id,this.value)" id="<?php echo $value_id.'3'; ?>" class="form-control text-right">
                                                </td>
                                                <td>
                                                    <input aria-label="Input field" type="text" value="<?php echo round($rbd[$budget['account_code']]['distribution_q4']); ?>" name="<?php echo $name.'4'; ?>"  onblur="totalcaluclationquaterly(this.id,this.value)" id="<?php echo $value_id.'4'; ?>" class="form-control text-right">
                                                </td>
                                                <td>
                                                    <input aria-label="Input field" type="text" readonly value="<?php echo round($rbd[$budget['account_code']]['distribution_tot']); ?>" name="<?php echo $name.'total'; ?>" id="<?php echo $value_id.'total'; ?>" class="form-control text-right">
                                                </td>
                                            </tr>
                                        <?php $l++; $z++;
} ?>
                                        <?php $cat_id = "mkt_".$k."_r_".$k."_c_";
                                        $cat_idss = "mkt_".$k;
                                        ?>
                                        <tr class="totalbudget" style="background-color: #d3d4d5; height: 40px;">
                                            <td style="padding-left: 10px;"> Total</td>

                                            <?php $line_total = 0;
                                             for($g =1 ; $g<5;$g++) { ?>
                                                <td class="text-right">
                                                    <div id="<?php echo $cat_id.$g; ?>" style="padding-right: 5px;">
                                                        <?php
                                                             echo $cat_item_lin_qu[$g];
                                                             $all_tt_qt[$g] = $all_tt_qt[$g]+$cat_item_lin_qu[$g];

                                                             $line_total = $line_total+$cat_item_lin_qu[$g];
                                                         ?>
                                                    </div>
                                                </td>
                                            <?php }
                                                $alltotal_qt = $alltotal_qt+$line_total;
                                             ?>

                                            <td class="text-right">
                                                <div id="<?php echo "mcat_total_".$k; ?>" style="padding-right: 5px;">
                                                <?php echo $line_total; ?>
                                                </div>
                                            </td>
                                        </tr>

                                        <input type="hidden" name="" id="<?php echo $cat_idss; ?>" value="<?php echo $l; ?>">
                                    <?php $k++;
}
                                }  ?>
                                <input type="hidden" name="" id="mgd_count" value="<?php echo $k; ?>">
                                <tr class="totalbudget" style="background-color: #88d98a; height: 40px;">
                                    <td style="padding-left: 10px;">Grand Total</td>
                                    <?php
                                    for($g =1 ; $g<5;$g++) { ?>
                                        <td class="text-right">
                                            <div id="<?php echo "mgd_".$g; ?>" style="padding-right: 5px;">
                                                <?php echo $all_tt_qt[$g]; ?>
                                            </div>
                                        </td>
                                        <input type="hidden" name="" id="<?php echo "mgdv_".$g; ?>" value="">
                                    <?php } ?>
                                    <td class="text-right" id="total_mgd" style="padding-right: 5px;">
                                        <?php echo $alltotal_qt; ?>
                                    </td>
                                    <input type="hidden" name="" id="total_mgdv" value="0">
                                </tr>
                            </table>
                        </div>
                        <!--- Quater Section END --->
                        <!--- Half Section Start --->
                        <div class="budget-table m-t-20" id="half" <?php if($view_type!='H'){?> style="display: none;" <?php
} ?>>
                            <h3>Budget Half Yearly</h3>
                            <table>
                                <tr style="background-color: #22b9ff; height: 40px;" >
                                    <th scope="col">Account</th>
                                    <th scope="col">Half Yearly 1</th>
                                    <th scope="col">Half Yearly 2</th>
                                    <th scope="col">Total</th>
                                </tr>
                                <?php $k=1; $z=1; foreach ($budget_data as $budget_details) { ?>
                                    <tr style="background-color: #bbb4b4; height: 40px;">
                                        <td style="padding-left: 5px;"><?php echo $budget_details['section_name'];?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?php

                                    foreach ($budget_details['data'] as $budgets) { ?>
                                        <tr style="background-color: #e8ca1f; height: 40px;" >
                                            <td style="padding-left: 10px;"><?php echo $budgets['group_name'];?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <?php $l=1 ;
                                        $cat_item_lin_hf = array();
                                        foreach ($budgets['details'] as $budget) {
                                            $name = "half_".$z."_";
                                            $value_id= "yk_".$k."_r_".$l."_c_";
                                            for ($n=1; $n <5 ; $n++) {
                                             $vvccff = "distribution_h".$n;
                                             $cat_item_lin_hf[$n] = $cat_item_lin_hf[$n]+$rbd[$budget['account_code']][$vvccff];
                                             }
                                        ?>
                                            <tr>
                                                <td class="padlft" style="padding-left: 15px; text-align: left;">    <?php echo $budget['account_name'];?>
                                                <input type="hidden" name="<?php echo $name.'account_code'; ?>" value="<?php echo $budget['account_code'];?>">
                                                </td>
                                                <td>
                                                    <input aria-label="Input field" type="text" value="<?php echo round($rbd[$budget['account_code']]['distribution_h1']); ?>" name="<?php echo $name.'1'; ?>"  onblur="totalcaluclationquaterly(this.id,this.value)" id="<?php echo $value_id.'1'; ?>" class="form-control text-right">
                                                </td>
                                                <td>
                                                    <input aria-label="Input field" type="text" value="<?php echo round($rbd[$budget['account_code']]['distribution_h2']); ?>" name="<?php echo $name.'2'; ?>"  onblur="totalcaluclationquaterly(this.id,this.value)" id="<?php echo $value_id.'2'; ?>" class="form-control text-right">
                                                </td>
                                                <td>
                                                    <input aria-label="Input field" type="text" readonly value="<?php echo round($rbd[$budget['account_code']]['distribution_tot']); ?>" name="<?php echo $name.'total'; ?>" id="<?php echo $name.'total'; ?>" class="form-control text-right">
                                                </td>
                                            </tr>
                                        <?php $l++; $z++;
} ?>
                                        <?php $cat_id = "ykt_".$k."_r_".$k."_c_";
                                        $cat_idss = "ykt_".$k;
                                        ?>
                                        <tr class="totalbudget" style="background-color: #d3d4d5; height: 40px;">
                                            <td style="padding-left: 10px;"> Total</td>

                                            <?php $line_total = 0;
                                             for($g =1 ; $g<3;$g++) { ?>
                                                <td class="text-right">
                                                    <div id="<?php echo $cat_id.$g; ?>" style="padding-right: 5px;">
                                                        <?php
                                                             echo $cat_item_lin_hf[$g];
                                                             $all_tt_hf[$g] = $all_tt_hf[$g]+$cat_item_lin_hf[$g];

                                                             $line_total = $line_total+$cat_item_lin_hf[$g];
                                                         ?>
                                                    </div>
                                                </td>
                                            <?php }
                                                $alltotal_hf = $alltotal_hf+$line_total;
                                             ?>

                                            <td class="text-right">
                                                <div id="<?php echo "ycat_total_".$k; ?>" style="padding-right: 5px;">
                                                <?php echo $line_total; ?>
                                                </div>
                                            </td>
                                        </tr>

                                        <input type="hidden" name="" id="<?php echo $cat_idss; ?>" value="<?php echo $l; ?>">
                                    <?php $k++;
}
                                }  ?>
                                <input type="hidden" name="" id="ygd_count" value="<?php echo $k; ?>">
                                <tr class="totalbudget" style="background-color: #88d98a; height: 40px;">
                                    <td style="padding-left: 10px;">Grand Total</td>
                                    <?php
                                    for($g =1 ; $g<3;$g++) { ?>
                                        <td class="text-right">
                                            <div id="<?php echo "ygd_".$g; ?>" style="padding-right: 5px;">
                                                <?php echo $all_tt_hf[$g]; ?>
                                            </div>
                                        </td>
                                        <input type="hidden" name="" id="<?php echo "ygdv_".$g; ?>" value="">
                                    <?php } ?>
                                    <td class="text-right" id="total_ygd" style="padding-right: 5px;">
                                        <?php echo $alltotal_hf; ?>
                                    </td>
                                    <input type="hidden" name="" id="total_ygdv" value="0">
                                </tr>
                            </table>
                        </div>
                        <!--- Half Section END --->
                        <!--- Yearly Section Start --->
                        <div class="budget-table m-t-20" id="yearly" <?php if($view_type!='Y'){?> style="display: none;" <?php
} ?>>
                            <h3>Budget  Yearly</h3>
                            <table>
                                <tr style="background-color: #22b9ff; height: 40px;" >
                                    <th scope="col">Account</th>
                                    <th scope="col"> Yearly </th>
                                </tr>
                                <?php $k=1; $z =1;
                                foreach ($budget_data as $budget_details) { ?>
                                    <tr style="background-color: #bbb4b4; height: 40px;">
                                        <td style="padding-left: 5px;"><?php echo $budget_details['section_name'];?></td>
                                        <td></td>
                                    </tr>
                                    <?php

                                    foreach ($budget_details['data'] as $budgets) { ?>
                                        <tr style="background-color: #e8ca1f; height: 40px;" >
                                            <td style="padding-left: 10px;"><?php echo $budgets['group_name'];?></td>
                                            <td></td>
                                        </tr>
                                        <?php $l=1 ;
                                        $cat_item_lin_yy = 0;
                                        foreach ($budgets['details'] as $budget) {
                                            $name = "year_".$z."_";
                                            $value_id= "lk_".$k."_r_".$l."_c_";
                                            $cat_item_lin_yy = $cat_item_lin_yy+$rbd[$budget['account_code']]['distribution_tot'];
                                        ?>
                                            <tr>
                                                <td class="padlft" style="padding-left: 15px; text-align: left;">    <?php echo $budget['account_name'];?>
                                                    <input type="hidden" name="<?php echo $name.'account_code'; ?>" value="<?php echo $budget['account_code'];?>">
                                                </td>
                                                <td>
                                                    <input aria-label="Input field" type="text" value="<?php echo round($rbd[$budget['account_code']]['distribution_tot']); ?>" name="<?php echo $name.'1'; ?>"  onblur="totalcaluclationyear(this.id,this.value)" id="<?php echo $value_id.'1'; ?>" class="form-control text-right">
                                                </td>

                                            </tr>
                                        <?php $l++; $z++;
} ?>
                                        <?php $cat_id = "lkt_".$k."_r_".$k."_c_";
                                        $cat_idss = "lkt_".$k;
                                        $cctt =  "lkt_".$k."_total";
                                        ?>
                                        <tr class="totalbudget" style="background-color: #d3d4d5; height: 40px;">
                                            <td style="padding-left: 10px;"> Total</td>


                                                <td class="text-right">
                                                    <div id="<?php echo $cat_id.$k; ?>" style="padding-right: 5px;">
                                                        <?php
                                                             echo $cat_item_lin_yy;
                                                         ?>
                                                    </div>
                                                </td>
                                                <input type="hidden" name="<?php echo $cctt;  ?>" value="0" id="<?php echo $cctt;  ?>">
                                            <?php
                                                $alltotal_yy = $alltotal_yy+$cat_item_lin_yy;
                                             ?>


                                        </tr>

                                        <input type="hidden" name="" id="<?php echo $cat_idss; ?>" value="<?php echo $l; ?>">
                                    <?php $k++;
}
                                }  ?>
                                <input type="hidden" name="" id="mgd_count_2" value="<?php echo $k; ?>">
                                <tr class="totalbudget" style="background-color: #88d98a; height: 40px;">
                                    <td style="padding-left: 10px;">Grand Total</td>

                                    <td class="text-right" id="total_lgd" style="padding-right: 5px;">
                                        <?php echo $alltotal_yy; ?>
                                    </td>
                                    <input type="hidden" name="" id="total_lgdv" value="0">
                                </tr>
                            </table>
                        </div>
                        <!--- Yearly Section END --->
                        <!--- Footer Submit Section Start --->
                        <div class="card-footer">

                            <div class="row">
                                <div class="col-lg-4">
                                    <input type="hidden" id="table_name" name="table_name" value="<?php echo $table_name; ?>" />
                                    <input type="hidden" id="count" name="count" value="<?php echo $z; ?>" />
                                    <input type="hidden" id="user_id" name="user_id" value=<?php echo $_SESSION['userid']; ?> />
                                    <input type="hidden" id="now_date" name="now_date" value="<?php echo date("Y-m-d H:i:s"); ?>" />
                                    <input type="hidden" name="old_place1" id="old_place1" value="<?php echo $location_state;?>">
                                    <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $copy == '1'){ ?>
                                        <input type="hidden" id="hid"name="hid" value="<?php echo $rid; ?>" />
                                        <input type="hidden" name="hid_itemds" value="<?php echo $item_ids; ?>" />
                                        <input type="hidden" name="submit1" value="Update" />
                                        <button id="kt_llogin_signup_submit" <?php echo $disable; ?> class="btn btn-primary mr-2" ><i class="la la-check"></i>Upadate</button>
                                        <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                                    <?php } else{ ?>
                                        <input type="hidden" name="submit1" value="Add" />
                                        <button id="kt_login_signin_submit" class="btn btn-primary mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
                                        <button id="kt_login_signin_submit_continue" class="btn btn-primary mr-2"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
                                        <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                                    <?php } ?>
                                </div>
                                <div class="col-lg-6">
                                    <?php if(count((array)$vdata_details) > 0){ ?>
                                        <div class="accordion accordion-light  accordion-toggle-arrow" id="accordionExample2">

                                            <div class="card">
                                                <div class="card-header" id="headingTwo2">
                                                    <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo2" aria-expanded="false" aria-controls="collapseTwo2">
                                                        Transaction Timeline
                                                    </div>
                                                </div>
                                                <div id="collapseTwo2" class="collapse" aria-labelledby="headingTwo2" data-parent="#accordionExample2">
                                                    <div class="card-body">
                                                        <div class="kt-timeline-v2">
                                                            <div class="kt-timeline-v2__items  kt-padding-top-25 kt-padding-bottom-30">
                                                                <?php foreach ($vdata_details as  $vdvalue) { //print_r($vdvalue); ?>
                                                                    <div class="kt-timeline-v2__item">

                                                                        <div class="kt-timeline-v2__item-cricle">
                                                                            <?php if($vdvalue['transaction_type'] == 'INSERT'){ ?>
                                                                                <i class="fa fa-genderless kt-font-success"></i>
                                                                            <?php } ?>
                                                                            <?php if($vdvalue['transaction_type'] == 'UPDATE'){ ?>
                                                                                <i class="fa fa-genderless kt-font-warning"></i>
                                                                            <?php } ?>
                                                                        </div>
                                                                        <div class="kt-timeline-v2__item-text  kt-padding-top-5">
                                                                            <b> <?php echo date("M d, Y g:i A", strtotime($vdvalue['action_date'])); ?></b>
                                                                            <?php echo " ".$vdvalue['action_log']. " ".$_SESSION['users'][$vdvalue['user_id']]." with Status ".$_SESSION['status'][$vdvalue['status_value']]['name']; ?>

                                                                        </div>
                                                                    </div>
                                                                <?php  } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-lg-2 kt-align-right">
                                    <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
                                </div>
                            </div>
                        </div>
                        <!--- Footer Submit Section END --->
                        <?php }else{ ?>
                            <div class="m-t-10">

                                <?php if(count((array)$budget_data)==0 ){ ?>
                                <div class="alert alert-danger background-danger alert-dismissible">
                                    <strong><?php echo $_SESSION['error_message']['9163']; ?></strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <?php } ?>
                                <?php if(count((array)$cost_centers)==0 ){ ?>
                                <div class="alert alert-danger background-danger alert-dismissible">
                                    <strong><?php echo $_SESSION['error_message']['9164']; ?></strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <?php } ?>

                            </div>
                        <?php } ?>
                    </form>

                </div>
            </div>
            <!--- Main Data Start --->
        </div>
    </div>
</div>
