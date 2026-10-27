<?php
if (isset($_POST['submit'])){
    $data= array();
    $data['report_name'] = $_POST['report_name'];
    $data['report_display_id'] = $_POST['report_display_id'];
    $data['content'] = urlencode($_POST['content']);
    $content_details ['tables']= json_decode($_POST['tables'],1);
    $content_details ['join_details']= json_decode($_POST['join_details'],1);
    $content_details ['join']= $_POST['join'];
    $content_details ['table']= $_POST['table'];
    $content_details ['left_field']= $_POST['left_field'];
    $content_details ['right_field']= $_POST['right_field'];
    $data['content_details'] =json_encode($content_details);
    $data['current_status'] = $_POST['current_status'];
    $data['status_date'] = nowDate();
    $data['from_date'] = dateFormate($_POST['from_date']);
    $data['to_date'] = dateFormate($_POST['to_date']);
    $data['company_id'] = $_SESSION['company_id'];
    if($_POST['submit'] == 'Add'){
        $data['created_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'];
        $url = API."?mode=adddata&table=".TBL_REPORT_ATTRIBUTES."&method=post";
        $response = file_post_contents($url, $data);
    } else{
        $data['updated_date'] = nowDate();
        $data['updated_by'] = $_SESSION['userid'];
        $url = API."?mode=updaterowbasepimarydata&rid=".$_POST['hid']."&table=".$table_name."&method=post";
        $response = file_post_contents($url, $data);
    }
    if ($response) {
        if ($_POST['submit_value'] == 'continue'){
                header("location:index.php?option=".$_GET['option']."&table=".$_POST['table_name']."&error=3000");
        }else{
                header("location:index.php?option=com_list_display_attributes&error=3000");
        }

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
    $attribute = json_decode($role['list_attributes'],1);
    $tableurl = API."?mode=singlerowtabledata&table=".TBL_REPORT_ATTRIBUTES."&rid=".$rid;
    $response = callAPI($tableurl);
    $data = json_decode($response,1);
    $cont = json_decode($data['content_details'],1);

    $main_table = $cont['table'];
    $tables =  $cont['tables'];
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
        $("#view_table").html("<select aria-label='Table' class='form-control' id ='table' name='table' onchange='fieldchange(this.value)'><option value=''>Select any table </option>"+option+"</select>");

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
            if( right != '0'){
                var table =  $('#table').val();
                arry['left_field'] = $('#left_field').val();
                arry['right_field'] =  $('#right_field').val();
                arry['join'] = $('#join_type').val();
                bquery [table] = arry;
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
        alert("ravi");
        leftfield(val);
        var data = $('#droplist').val();
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
                    $("#right_field_id").html("<select aria-label='Right Field' class='form-control' onchange='bulidquery(this.value,this.id)' id ='right_field' name='right_field'>"+data+"</select>");
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
                    $("#left_field_id").html("<select aria-label='Left Field' class='form-control' onchange='bulidquery(this.value,this.id)' id ='left_field' name='left_field'>"+data+"</select>");
            },
            error:function (){}
        });
    }

</script>
<div class="d-flex flex-column-fluid">
<div class="container-fluid">
    <?php if($content !=''){ ?>
    <div class="alert alert-light alert-elevate" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text">
            <?php echo $content; ?>
        </div>
    </div>
<?php } ?>
    <div class="row">
        <div class="col-lg-12">
            <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
            <!--begin::Portlet-->
            <div class="card card-custom gutter-b card-stretch" id="kt_page_portlet">
                <div class="card-header">
                    <div class="kt-portlet__head-label float-left">
                        <span class="kt-portlet__head-icon">

                                </span>
                        <h3 class="kt-portlet__head-title"><?php echo "Join Tables";  ?> </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar float-right">
                        <a href="javascript:history.go(-1)" class="btn btn-clean kt-margin-r-10">
                            <i class="la la-arrow-left"></i>
                            <span class="kt-hidden-mobile">Back</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="form-group col-lg-6">
                            <span>Report Name</span>
                            <div class="kt-input-icon">
                                <input aria-label="Report Name" type="text" name="report_name" value="<?php echo $data['report_name']; ?>" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span>Report  Display</span>
                            <div class="kt-input-icon">
                                <input aria-label="Report Display" type="text" name="report_display_id" value="<?php echo $data['report_display_id']; ?>" class="form-control">
                            </div>
                        </div>
                        <?php if($rid !=""){ ?>
                        <div class="form-group col-lg-6">
                            <span>Report Code</span>
                            <div class="kt-input-icon">
                                <input aria-label="Report Code" type="text"  readonly name="report_code" value="<?php echo $data['report_code']; ?>"  class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span>Report  Fields</span>
                            <div class="kt-input-icon">
                                <a href="index.php?option=COM_FIELD_REPORT_ATTRIBUTES&Rid=<?php echo $rid; ?>" target="_blank">
                                <button type="button" class="btn btn-primary"  >Report  Fields</button>
                            </a>

                            </div>
                        </div>
                        <?php  } ?>
                        <div class="form-group col-lg-6">
                            <span>Join Tables</span>
                            <div class="kt-input-icon">

                                <select aria-label="Select Tables" id="kt_dual_listbox_1_2" class="kt-dual-listbox" multiple data-available-title="Selet Tables" data-selected-title="Joined Tables" >
                                    <?php foreach ($showtables as $table) { ?>
                                    <option <?php if(in_array($table, (array)$tables)){?> selected <?php
} ?> value="<?php echo $table; ?>"><?php echo ucwords(str_replace('_', ' ', $table)); ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span>Join Conditions</span>
                            <?php $drop_list=  count((array)$tables)>0 ? json_encode($tables) : "[]"; ?>
                            <input type="hidden" name="tables" class="form-control" value='<?php echo $drop_list; ?>' id="droplist">
                            <div class="form-group kt-input-icon">
                                <span>Report Tables</span>
                                <div id='view_table'>
                                <select aria-label="Table" class="form-control kt-select2" id='table_2' onchange="fieldchange(this.value)">
                                    <option value=""> Select any table </option>
                                    <?php $i=0; foreach ($tables as $tab) {
                                        if($tab != $main_table){?>
                                        <option value="<?php echo $tab; ?>"> <?php echo str_replace('_', ' ', $tab); ?></option>
                                    <?php } ?>
                                    <?php } ?>

                                </select>
                                </div>

                            </div>
                            <div class="form-group kt-input-icon">
                                <span>Join Type</span>
                                <select aria-label="Join Type" class="form-control kt-select2" id='join_type'>
                                    <option value="INNER JOIN"> INNER JOIN </option>
                                    <option value="LEFT JOIN"> LEFT JOIN</option>
                                    <option value="RIGHT JOIN"> RIGHT JOIN</option>
                                </select>
                            </div>
                            <div class="form-group kt-input-icon">
                                <span>Left Field</span>
                                <div id='left_field_id'>
                                    <select aria-label="Left Field" class="form-control" id='left_field_2' name="left_field">
                                        <option value="0"> Select any field</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group kt-input-icon">
                                <span>Right Field</span>
                                <div id="right_field_id">
                                    <select aria-label="Right Field" class="form-control" id='right_field_2' name="right_field">
                                        <option value="0"> Select any field</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group kt-input-icon">
                                <span>Query</span>
                                <input type="hidden" name="join" id="join" value="0" class="form-control" >
                                <input type="hidden"  id="bquery" value="" name="join_details" class="form-control" >
                                <textarea aria-label="Content" type="text" class="form-control" id="bulid_query" name="content" value="" ><?php echo urldecode($data['content']);?></textarea>
                            </div>
                        </div>
                        <?php include_once 'submit.php'; ?>
                    </div>

                </div>

            </div>

        </form>
    </div>
</div>
</div>
</div>
