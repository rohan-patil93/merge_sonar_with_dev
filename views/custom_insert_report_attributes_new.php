<?php
$_SESSION['apply_js']['tag']='1';
$_SESSION['apply_js']['custom_insert_my_reports']='1';
$_SESSION['apply_js']['multiselest'] = 1;
if (isset($_POST['submit'])){
    $data= array();
    $data['user_id'] = $_SESSION['userid'];
    $data['company_id'] = $_SESSION['company_id'];
    $data['report_name'] = $_POST['report_name'];
    $data['module_id']=$_SESSION['module_setp'][0]['module_id'];
    $data['module_group_id']=$_SESSION['module_setp'][0]['module_group_id'];
    $data['report_display_id'] = $_POST['report_display_id'];
    $data['report_id']= $_POST['Rid'];
    $data['content'] = urlencode($_POST['content']);
    $content_details['selectedtables']=  $_POST['selectedtables'];
    $content_details['tablefiledsselect']= $_POST['tablefiledsselect'];
    $content_details['tables']= $_POST['tables'];
    $content_details['wherefield']= $_POST['wherefield'];
    $content_details['whereoperators']= $_POST['whereoperators'];
    $content_details['wherevaluemoption']= $_POST['wherevaluemoption'];
    $content_details['whereconditions']= $_POST['whereconditions'];
    $content_details['groupby']= $_POST['groupby'];
    $content_details['orderby']= $_POST['orderby'];
    $content_details['ordertype']= $_POST['ordertype'];
    $content_details['orderbycheck']= (isset($_POST['orderbycheck']))?0:1;
    $content_details['groupbycheck']= (isset($_POST['groupbycheck']))?0:1;
    $content_details['placeholderslist']= $_POST['placeholderslist'];
    $content_details['formulaplaceholder']= $_POST['formulaplaceholder'];
    $data['content_details'] =urlencode(json_encode($content_details));
    $data['is_active'] = '0';
    $data['current_status'] = '0';
    $url = API."?mode=insertreportattributesnew&method=post";
    $response = file_post_contents($url, $data);
    $response = json_decode($response,1);
    if($_POST['Rid']==""){
        header("location:index.php?option=COM_INSERT_REPORT_ATTRIBUTES_NEW&Rid=".deocdeId("id:".$response['LASTID']));
    }else{
        header("location:index.php?option=COM_INSERT_REPORT_ATTRIBUTES_NEW&Rid=".deocdeId("id:".$_POST['Rid']));
    }
}

if((isset($_GET['table']) && $_GET['table'] !='') || $_GET['Rid']){
    $table = $_GET['table'];
    $rid= encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $tableurl = API."?mode=displayattribute&dis_table=$table&rid=$rid";
    $response = callAPI($tableurl);
    $role = json_decode($response,1);
    $table = $table!="" ? $table : $role['application_table_name'];
    $attributenew = json_decode($role['list_attributes'],1);
    $tableurl = API."?mode=singlerowtabledata&table=".TBL_REPORT_ATTRIBUTES."&rid=".$rid;
    $response = callAPI($tableurl);
    $data = json_decode($response,1);
    $cont = json_decode(urldecode($data['content_details']),1);
    foreach($cont['selectedtables'] as $key => $tabledata){
        $fileget = getListAttributes($tabledata);
        foreach($fileget as $ros){
            $valkey = $tabledata.".".$ros['Field'];
            $allfiledsarray[] = $valkey;
        }
    }
}
$tdurl = API."?mode=showtables";
$showtablesdata = callAPI($tdurl);
$showtables = json_decode($showtablesdata,1);

?>
<script type="text/javascript">
    function droplist(val,type){
        alert("hai");
        var data = $('#droplist').val();
        data = JSON.parse(data);
        if(type == 'add'){
            data.push(val);
        }
        if(type == 'remove'){
            data = data.filter(function(item) { return item !== val });
        }
        var option ="";
        var i=0;
        data.forEach(function(entry) {
            if(i>0){
                option +="<option value = '"+entry+"'>"+entry.replace("_", " ")+"</option>";
            }
            i++;
        });
        $("#view_table").html("<select aria-label='Table' class='form-control form-select js-example-basic-single' id ='table' name='table' onchange='fieldchange(this.value)'><option value=''>Select any table </option>"+option+"</select>");
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();

        console.log(option);
        var chan = JSON.stringify(data);
        $('#droplist').val(chan);
        bulidquery(val,'table');

    }
    function bulidquery(val,type){
        var bquery = $('#bquery').val();
        if(bquery == ''){
            bquery={};
        }else{
            bquery = JSON.parse(bquery);
        }
        console.log(bquery);
        var arry ={};
        if(type == 'right_field' || type == 'left_field'){
            var right =$('#right_field').val();
            alert(right);
            if(right != '0'){
                var table =  $('#table').val();
                arry['left_field'] = $('#left_field').val();
                arry['right_field'] =  $('#right_field').val();
                arry['join'] = $('#join_type').val();
                bquery [table] = arry;
                console.log(bquery);
                $('#join').val(1);
            }
        }
        console.log(bquery);
        bquery = JSON.stringify(bquery);
        $('#bquery').val(bquery);
        createquery();
    }
    function createquery(){
        var data = $('#bquery').val();
        var table = $('#droplist').val();
        console.log ('bquery');
        console.log (data);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=custom_query_join&table='+table+'&data='+data,
            type: "POST",
            success:function(data){
                console.log(data);
                $('#bulid_query').val(data);

            },
            error:function (){}
        });

    }
    function fieldchange(val){
        leftfield(val);
        var data = $('#droplist').val();
        console.log(data);
        data = JSON.parse(data);
        data = data.filter(function(item) { return item !== val });
        var chan = JSON.stringify(data);
        rightfiled(chan);

    }
    function rightfiled(val){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=custom_right_join&table='+val,
            type: "POST",
            success:function(data){
                $("#right_field_id").html("<select aria-label='Right Field' class='form-control form-select js-example-basic-single' onchange='bulidquery(this.value,this.id)' id ='right_field' name='right_field'>"+data+"</select>");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
            },
            error:function (){}
        });
    }
    function leftfield(val){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=dis_att_type&table='+val,
            type: "POST",
            success:function(data){
                $("#left_field_id").html("<select aria-label='Left Field' class='form-control form-select js-example-basic-single' onchange='bulidquery(this.value,this.id)' id ='left_field' name='left_field'>"+data+"</select>");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
            },
            error:function (){}
        });
    }

function selectTablename(){

    var value = $(".selecttablename").val();
    console.log(value);
    var lenghttabe = value.length;
    var olddata = $(".selecttablename").val();
        var data = $('#droplist').val();
        $('#droplist').val(JSON.stringify(value));
        if(data !=""){
            data = JSON.parse(data);
            data.forEach(function(entry) {
                value.splice($.inArray(entry, value), 1);
                var  chk = olddata.indexOf(entry) >= 0;
                if(!chk){
                    $(".tablenameclass"+entry).remove();
                }
            });
        }

        if(!jQuery.isEmptyObject(value)){
            jQuery.ajax({
                url: "ajax.php",
                data:'page=selecttablenamefiledlist&table='+value,
                type: "POST",
                success:function(data){
                    console.log(data);

                    var data  = JSON.parse(data);

                    $(".tablefiledsset").append(data.option);
                    $(".js-example-basic-multiple").select2();
                },
                error:function (){}
            });

        }
        getGroupBy();
        slectfeildsset();
        buildQuery();
    }
    function slectfeildsset(){
        const fields = [];
        var option="";
        $(".selecttablename").each(function(index) {
            var arayfileds = $( this ).val() ;
            arayfileds.forEach(function(val) {
                fields.push(val);
            });
        });
        fields.forEach(function(vall){
            option += "<option value='"+vall+"'>"+vall+"</option>";
        });
        $(".basetableclass").html(option);
    }

    function buildQuery(){
        setTimeout(function() {
            var query = "SELECT ";
            var select = "";
            var basetable = $(".basetableclass").val()
            var table = droplist;
            var form = ""
            if($('.doyouwantcondition').prop('checked')){
                form = firsttable[0];
            }else {
                form = $(".selecttablename").val().reverse().join(', ');
            }
            var abc = [];
            $(".tablefiledsselect").each(function(index){
                if($(this).val().length == 0){
                    abc.push($(this).attr("data-table")+".*");
                }else{
                    $(this).val().forEach(function(val){
                        var replace_name = val.replace(".", "_");
                        abc.push(val+" AS "+replace_name);
                    });
                }
            });
            select += abc.join(', ');
var wherefield= [];
$(".wherefield").each(function( index ) {
    wherefield[index] = $(this).val();
});
var whereoperators= [];
$(".whereoperators").each(function( index ) {
    whereoperators[index] = $(this).val();
});
var wherevaluem = [];
$(".wherevaluem").each(function( index ) {
    wherevaluem[index] = $(this).val();
});
var whereconditions  = [];
$(".whereconditions").each(function( index ) {
    whereconditions[index] = $(this).val();
});
var where = "";
$(".wherefield").each(function( index ){
    if(index != 0){
        where += " "+whereconditions[index]+" ";
    }else{
        where +=" WHERE ";
    }
    where += wherefield[index]+" "+whereoperators[index]+" "+wherevaluem[index];
});
var groupby = " ";
var orderby =" "
if($(".groupby").val()!="" && $('.doyouwantconditiongroupby').prop('checked')){
    groupby = " GROUP BY "+$(".groupby").val().join(', ');
}
if($(".orderby").val()!="" && $('.doyouwantconditionorderby').prop('checked')){
    orderby = " ORDER BY "+$(".orderby").val().join(', ')+" "+$(".ordertype").val();
}
query +=select==""?"*":select;
query +=" FROM "+form;
query += where;
query += groupby;
query += orderby;
if(form==""){
    $("#bulid_query").val("");
}else{
    $("#bulid_query").val(query);
}
}, 1000);
}
//         url: "views/custom_insert_my_reports.php",
//         data:'attributeid='+rid,
//         type: "GET",
function deletejoinrow(id){
    $(".joinrow"+id).remove();
    buildQuery();
}
function deletewhererow(id){
    $(".wharerow"+id).remove();
    buildQuery();
}
function getGroupBy(){
    var value = $(".selecttablename").val();
    value = value.reverse();
    jQuery.ajax({
        url: "ajax.php",
        data:'page=selectfiledlists&table='+value,
        type: "POST",
        success:function(data){
            var data  = JSON.parse(data);
            $(".groupby").html(data.option);
            $(".orderby").html(data.option);
            $(".js-example-basic-single").select2();
            $(".js-example-disabled-results").select2();
        },
        error:function (){}
    });
}
//         url: "ajax.php",
//         data:'page=selectfiledlists&table='+value,
//         type: "POST",
var whmax = 100;
function whereClouseSet(){
    var value = $(".selecttablename").val();
    if(value==""){
        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',"Please select Tables first",'');
    }else{
        value = value.reverse();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=selectfiledlists&table='+value,
            type: "POST",
            success:function(data){
                console.log(data);
                var data  = JSON.parse(data);
                var rows = '<tr class="wharerow'+whmax+' allremove"><td><button type="button" class="btn p-0 border-0 bg-transparent fa fa-trash" onclick="deletewhererow('+whmax+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></td><td class="w-full"><select aria-label="Wherefield[]"  class="kt-dual-listbox form-select js-example-basic-single wherefield"  onchange="buildQuery()" name="wherefield[]">'+data.option+'</select></td><td class="w-full"><select aria-label="Whereoperators[]"  class="kt-dual-listbox form-select js-example-basic-single whereoperators" name="whereoperators[]"  onchange="buildQuery()" ><option value="="> = </option><option value="!=">!=</option><option value=">"> > </option><option value="<"> < </option><option value=">="> >= </option><option value="<="> <= </option></select></td><td><div class="textorfeild'+whmax+' w-full"><select aria-label="Wherevaluemoption[]"  class="kt-dual-listbox form-select js-example-basic-single wherevaluem" name="wherevaluemoption[]"  onchange="valueselect(this.value,'+whmax+')" >'+data.option+'<option value="{[emp_id]}">Login Employee ID</option><option value="{[user_id]}">Login User ID</option><option value="text">Text</option></select></div></td><td class="w-full"><select aria-label="Whereconditions[]"  class="kt-dual-listbox form-select js-example-basic-single whereconditions"  onchange="buildQuery()"  name="whereconditions[]"><option value="AND">AND  </option><option value="OR">OR</option></select></td></tr>';
                $(".wherecondition").append(rows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
    },
    error:function (){}
});
        whmax++;
    }
}
function valueselect(val,id){
    if(val == 'text'){
        $(".textorfeild"+id).empty();
        $(".textorfeild"+id).append('<input aria-label="eg.0,name,date" class="wherevaluem form-control" type="text" name="wherevaluemoption[]"  onblur="buildQuery()" placeholder="eg.0,name,date">');
    }
    buildQuery();
}
function checkjoinconditiondisplay(){
    if($('.doyouwantcondition').prop('checked')){
        $(".jointabledata").show();
    }else {
        $(".jointabledata").hide();
    }
    buildQuery();
}

function selecttableenable(){

    if($(".selecttableenable").is(":checked")){
        Swal.fire({
            title: 'Any table add or remove all data is reset.',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'OK',
            denyButtonText: `NO`,
        }).then((result) => {
            if (result.isConfirmed) {
                $(".selecttablename").prop('disabled', false);
} else if (result.isDenied) {
  $('.selecttableenable').removeAttr('checked');
  $(".selecttablename").prop('disabled', true);
}
})
    }else{
        $(".selecttablename").prop('disabled', true);
    }
}

function doyouwantconditiongroupby(){
    if($(".doyouwantconditiongroupby").is(":checked")){
        $(".doyouwantconditiongroupbydiv").show();
    }else {
        $(".doyouwantconditiongroupbydiv").hide();
    }
    buildQuery();
}
function doyouwantconditionorderby(){
    if($('.doyouwantconditionorderby').prop('checked')){
        $(".doyouwantconditionorderbydiv").show();
    }else {
        $(".doyouwantconditionorderbydiv").hide();
    }
    buildQuery();
}

function copyToClipboard(id) {
    var textBox = document.getElementById("text_"+id);
    textBox.select();
    document.execCommand("copy");
}

function sessionModal(){
    $('#session_modal').modal("show");
}
</script>

<div class="card card-custom">
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <ul class="nav md-tabs infisuite_tab mt-3 m-l-15">
                <li class="nav-item">
                    <a class="nav-link <?php echo (!isset($_GET['Rid']))?"active":""; ?>" data-bs-toggle="tab" href="#home3" role="tab"> <i class="fa fa-database"></i> Build Query</a>
                    <div class="slide"></div>
                </li>
                <?php if(isset($_GET['Rid'])){ ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($_GET['Rid']))?"active":""; ?>" data-bs-toggle="tab" href="#profile3" onclick="GetMyReportPage()" role="tab"> <i class="fa fa-file"></i> Build Reports</a>
                        <div class="slide"></div>
                    </li>
                <?php } ?>
            </ul>
            <div class="tab-content card-block">
                <div class="tab-pane <?php echo (!isset($_GET['Rid']))?"active":""; ?>" id="home3" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5>Build Query</h5>
                            <div class="card-header-right">
                                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                    <i class="icofont icofont-reply"></i>
                                    Back
                                </a>
                            </div>
                        </div>
                        <form class="kt-form" id="kt_form" method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <input type="hidden" name="tables" class="form-control" value='<?php echo $cont['tables']; ?>' id="droplist">
                                <input type="hidden" name="usedtablelist" class="form-control" value='' id="usedtablelist">
                                <input type="hidden" name="Rid" id="Rid" class="form-control" value="<?php echo $rid; ?>">
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <span class="form-label">Report Name</span>
                                        <div class="kt-input-icon">
                                            <input aria-label="Report Name" type="text" name="report_name" value="<?php echo $data['report_name']; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6" style="display:none">
                                        <span class="form-label">Report  Display Id</span>
                                        <div class="kt-input-icon">
                                            <input aria-label="Report Display" type="text" name="report_display_id" value="1" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6 w-full">
                                        <span class="form-label">Select Tables  </span>
                                        <select aria-label="Select Tables" id="kt_dual_listbox_1" class="kt-dual-listbox form-select js-example-basic-multiple selecttablename" multiple data-available-title="Selet Tables" data-selected-title="Joined Tables" name="selectedtables[]" onchange="selectTablename()" <?php if(isset($_GET['Rid'])){ ?> readonly <?php
} ?>>
                                            <?php foreach ($showtables as $table) {        ?>
                                                <option <?php if(in_array($table, (array)$cont['selectedtables'])){?> selected <?php
} ?> value="<?php echo $table; ?>"><?php echo ucwords(str_replace('_', ' ', $table)); ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger">Note : Any table add or remove all data is reset.</span>
                                    </div>
                                    <div class="row tablefiledsset mb-2">
                                        <?php
                                        foreach($cont['selectedtables'] as $key){
                                            $resultfiled = getListAttributes($key);
                                            $tabledata = $cont['tablefiledsselect'][$key];
                                            ?>

                                            <div class="col-lg-3 tablenameclass<?php echo $key; ?> w-full">
                                                <span class="form-label"><?php echo ucwords(str_replace('_', ' ', $key)); ?></span>
                                                <select aria-label="Select option" class="form-select js-example-basic-multiple tablefiledsselect" onchange="buildQuery()" data-table="<?php echo $key; ?>" name="tablefiledsselect[<?php echo $key; ?>][]"   multiple>
                                                    <?php foreach($resultfiled as $tabl){  ?>
                                                        <option value="<?php echo $key.".".$tabl['Field']; ?>"   <?php if(in_array($key.".".$tabl['Field'], (array)$tabledata)){?> selected <?php
} ?> ><?php echo ucwords(str_replace('_', ' ', $tabl['Field'])); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="row mt-2 m-l-5">
                                        <span class="f-w-600">Condition</span>
                                        <table class="table table-hover " style="border-radius: 10px;" id="invoiceItem">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class=""></th>
                                                    <th scope="col" class="" >Field </th>
                                                    <th scope="col">Operator</th>
                                                    <th scope="col" class="" >Value</th>
                                                    <th scope="col" class="" >Condition</th>
                                                </tr> </thead>
                                                <tbody class="wherecondition">
                                                    <?php $whmax = 0; foreach($cont['wherefield'] as $key=>$keys){ ?>
                                                        <tr class="wharerow<?php echo $whmax; ?> allremove">
                                                            <td><i class="fa fa-trash" onclick="deletewhererow('<?php echo $whmax; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></td>

                                                            <td class="w-full"><select aria-label="Wherefield[]"  class="kt-dual-listbox form-select js-example-basic-single wherefield"  onchange="buildQuery()" name="wherefield[]">
                                                                <?php foreach($allfiledsarray as $opt){ ?>
                                                                    <option value="<?php echo $opt; ?>" <?php if($opt == $cont['wherefield'][$key]){ ?> selected <?php
} ?> ><?php echo $opt; ?></option>
                                                                <?php } ?>
                                                            </select></td>

                                                            <td class="w-full"><select aria-label="Whereoperators[]"  class="kt-dual-listbox form-select js-example-basic-single whereoperators" name="whereoperators[]"  onchange="buildQuery()" ><option value="=" <?php echo ("=" == $cont['whereoperators'][$key])?"selected":""; ?> > = </option><option value="!=" <?php echo ("!=" == $cont['whereoperators'][$key])?"selected":""; ?>>!=</option><option value=">"> <?php echo (">" == $cont['whereoperators'][$key])?"selected":""; ?>> </option><option value="<" <?php echo ("<" == $cont['whereoperators'][$key])?"selected":""; ?>> < </option><option value=">="> <?php echo (">=" == $cont['whereoperators'][$key])?"selected":""; ?>>= </option><option value="<=" <?php echo ("<=" == $cont['whereoperators'][$key])?"selected":""; ?>> <= </option></select></td>
                                                            <td><div class="textorfeild<?php echo $whmax; ?>">
                                                                <?php
                                                                if(in_array($cont['wherevaluemoption'][$key],(array)$allfiledsarray)){
                                                                    ?>

                                                                    <select aria-label="Wherevaluemoption[]"  class="kt-dual-listbox form-select js-example-basic-single wherevaluem" name="wherevaluemoption[]"  onchange="valueselect(this.value,'<?php echo $whmax; ?>')" ><?php foreach($allfiledsarray as $opt){ ?>
                                                                        <option value="<?php echo $opt; ?>" <?php if($opt == $cont['wherevaluemoption'][$key]){ ?> selected <?php
} ?> ><?php echo $opt; ?></option>
                                                                    <?php } ?>
                                                                    <option <?php if( $cont['wherevaluemoption'][$key] == "[{emp_id}]"){ ?> selected <?php
} ?> value="[{emp_id}]">Login Employee ID</option>
                                                                    <option <?php if( $cont['wherevaluemoption'][$key] == "[{user_id}]"){ ?> selected <?php
} ?> value="[{user_id}]">Login User ID</option>
                                                                    <option value="text">Text</option>
                                                                </select>
                                                            <?php }else{ ?>
                                                                <input aria-label="Wherevaluemoption[]" class="wherevaluem form-control" type="text" name="wherevaluemoption[]"  value="<?php echo $cont['wherevaluemoption'][$key]; ?>" onblur="buildQuery()">
                                                            <?php } ?>

                                                        </div>
                                                    </td>
                                                    <td class="w-full">
                                                        <select aria-label="Whereconditions[]"  class="kt-dual-listbox form-select js-example-basic-single whereconditions"  onchange="buildQuery()"  name="whereconditions[]"><option value="AND" <?php echo ("AND" == $cont['whereconditions'][$key])?"selected":""; ?>>AND  </option><option value="OR" <?php echo ("OR" == $cont['whereconditions'][$key])?"selected":""; ?>>OR</option></select></td>
                                                    </tr>
                                                    <?php $whmax++;
} ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row m-l-5"><button type="button" class="btn btn-info waves-effect waves-light col-lg-3" onclick="whereClouseSet()">Add More Where</button></div>
                                        <div class="row mt-4">
                                            <div class="form-group kt-input-icon col-lg-6">
                                                <div class="form-group kt-input-icon">
                                                    <span class="form-label">Do You Want Group By ? </span>
                                                    <input aria-label="Groupbycheck" type="checkbox" name="groupbycheck" class="js-switch ml-2 doyouwantconditiongroupby" <?php echo $cont['groupbycheck']=='0'?"checked":""; ?> onchange="doyouwantconditiongroupby()" >
                                                </div>
                                                <div class="doyouwantconditiongroupbydiv w-full"  style="display: <?php echo $cont['groupbycheck']=='0'?"":"none"; ?>">
                                                    <span class="form-label">Group By</span>
                                                    <select aria-label="Groupby[]"  class="form-select js-example-basic-multiple groupby" name="groupby[]" multiple  onchange="buildQuery()">
                                                        <?php foreach($allfiledsarray as $opt){ ?>
                                                            <option value="<?php echo $opt; ?>" <?php if(in_array($opt, (array)$cont['groupby'])){ ?> selected <?php
} ?> ><?php echo $opt; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group kt-input-icon col-lg-4">
                                                <div class="form-group kt-input-icon">
                                                    <span class="form-label">Do You Want Order By ? </span>
                                                    <input aria-label="Checkbox" type="checkbox" class="js-switch ml-2 doyouwantconditionorderby"  <?php echo $cont['orderbycheck']=='0'?"checked":""; ?> name="orderbycheck" onchange="doyouwantconditionorderby()" >
                                                </div>
                                                <div class="doyouwantconditionorderbydiv w-full" style="display: <?php echo $cont['orderbycheck']=='0'?"":"none"; ?>">
                                                    <span class="form-label">Order By</span>
                                                    <select aria-label="Orderby[]"  class="form-select js-example-basic-multiple orderby"  name="orderby[]" multiple onchange="buildQuery()">
                                                        <?php foreach($allfiledsarray as $opt){ ?>
                                                            <option value="<?php echo $opt; ?>" <?php if(in_array($opt, (array)$cont['orderby'])){ ?> selected <?php
} ?> ><?php echo $opt; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group kt-input-icon col-lg-2 doyouwantconditionorderbydiv mt-5 w-full" style="display: <?php echo $cont['orderbycheck']=='0'?"":"none"; ?>">
                                                <span class="form-label">Order type</span>
                                                <select aria-label="Ordertype" class="kt-dual-listbox form-select js-example-basic-single ordertype" name="ordertype"  onchange="buildQuery()">
                                                    <option value="ASC" <?php echo ("ASC" == $cont['ordertype'])?"selected":""; ?>>ASC</option>
                                                    <option value="DESC" <?php echo ("DESC" == $cont['ordertype'])?"selected":""; ?>>DESC</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class=" row">
                                            <div class="form-group col-lg-6">
                                                <span class="form-label d-block">Placeholder <small class="text-danger">* please don't use space</small></span>
                                                <input aria-label="Placeholderslist" class="tags_add_multiple" type="text" id="idAddPlaceholders" name="placeholderslist" value="<?php echo $cont['placeholderslist']; ?>" onchange="addPlaceholders11()" data-role="tagsinput" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)">
                                            </div>
                                        </div>
                                        <input type="hidden" name="placeholderjson" id="placeholderjson">
                                        <div id="appendplaceholder">
                                            <?php foreach($cont['formulaplaceholder'] as  $placholdekey => $placeholderres){
                                                $placename = explode("@", $placholdekey);
                                                ?>
                                                <div class="row" id="placeholderrow<?php echo $placename[1]; ?>">
                                                    <div class="col-lg-4"><span class="form-label">Mapping placeholder <i class="ti-files cursor-pointer" onclick="copyToClipboard('<?php echo $placename[1]; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Copy To Use Placeholder" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></span>
                                                        <input aria-label="Input field" input type="text" id="text_<?php echo $placename[1]; ?>"  readonly class="form-control" value="<?php echo $placholdekey; ?>" >
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <span class="form-label">Sql Formula/Sub Sql</span>
                                                        <textarea aria-label="Text area" type="text" class="form-control" rows="4" cols="50"   name="formulaplaceholder[@<?php echo $placename[1]; ?>]" ><?php echo $placeholderres; ?></textarea>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="form-group kt-input-icon">
                                            <span>Query</span>
                                            <textarea aria-label="Content" type="text" class="form-control" id="bulid_query" rows="4" cols="50" name="content" value="" ><?php echo urldecode($data['content']);?></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-5">
                                        <button class="btn btn-primary" name="submit" value="submit">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane <?php echo (isset($_GET['Rid']))?"active":""; ?>" id="profile3" role="tabpanel">
                        <?php include_once 'views/custom_insert_my_reports.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="session_modal" class="modal fade" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg " >
            <div class="modal-content"  >
                <div class="modal-header">
                    <h4 class="modal-title">Session Fields</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <table class="table table-hover m-b-0">
                        <thead>
                            <tr><th scope="col">Used Field</th><th scope="col">Field</th><th scope="col">Value</th></tr>
                        </thead>
                        <tbody>
                            <tr>

                                <td>#userid</td>
                                <td>User Id</td>
                                <td><?php echo $_SESSION['userid']; ?></td>
                            </tr>
                            <tr>
                                <td>#email</td>
                                <td>Email Id</td>
                                <td><?php echo $_SESSION['email']; ?></td>
                            </tr>
                            <tr>
                                <td>#emp_id</td>
                                <td>Employee Id</td>
                                <td><?php echo $_SESSION['emp_id']; ?></td>
                            </tr>
                            <tr>
                                <td>#current_date</td>
                                <td>Current date</td>
                                <td><?php echo date("Y-m-d"); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails()">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function addPlaceholders11(){
            var idAddPlaceholders = $("#idAddPlaceholders").val();
            var placholderelisttext =idAddPlaceholders.split(",");
            var returnhtml = "";
            var jsonarray = [];
            var placeholderjson  = $("#placeholderjson").val();
            if(placeholderjson != ""){
                var placeholderjsons = JSON.parse(placeholderjson);
            }
            if(idAddPlaceholders !=""){
                $.each(placholderelisttext, function(key, value){
                    jsonarray.push(value);
                    var element = document.getElementById("placeholderrow"+value);
                    console.log(element);
                    if (element == null){
                        returnhtml += '<div class="row" id="placeholderrow'+value+'"><div class="col-lg-4"><span class="form-label">Mapping placeholder <button type="button" class="btn p-0 border-0 bg-transparent ti-files cursor-pointer" onclick="copyToClipboard(\''+value+'\')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Copy To Use Placeholder" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></span><input aria-label="Text" type="text"  readonly class="form-control" value="@'+value+'"  id="text_'+value+'"></div><div class="col-lg-8"><span class="form-label">Sql Formula/Sub Sql</span><textarea aria-label="Formulaplaceholder[@" type="text" class="form-control" rows="4" cols="50"   name="formulaplaceholder[@'+value+']"></textarea></div></div>';
                    }else{
                        console.log("ok");
                    }
                });
            }
            if(placeholderjson != ""){
                var intersections = placeholderjsons.filter(e => jsonarray.indexOf(e) == -1);
            $.each(intersections, function(keyyy, value11){ // remove not selected row
                $("#placeholderrow"+value11).remove();
                console.log("placeholderrow"+value11);
            });
        }
        $('#placeholderjson').val(JSON.stringify(jsonarray));
        $("#appendplaceholder").append(returnhtml);
         $('[data-bs-toggle="tooltip"]').tooltip();
    }
</script>
