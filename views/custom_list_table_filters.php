<?php
$_SESSION['apply_js']['multiselest'] = 1;
$table_name = $_GET['TBALE_NAME'];
$page_title_get = (explode("_",$table_name));
$page_title= implode(" ",(array)$page_title_get);
$vurl = API."?mode=gettablefilter&table=".$table_name."&admin_type=".$_SESSION['admin_type'];
$detailrunres = callAPI($vurl);
$tabledetails = json_decode($detailrunres,1);

$type_array = array('select','multi_select','disable_select','radio','checklist','auto_select');
foreach($tabledetails['diplay_attribute_fieldtype'] as $filekey=>$filevalue){
    $tablefiledslist[$filevalue['table_filed']] = $filevalue['lable'];
    $tablefileddtype[$filevalue['table_filed']] = $filevalue['type'];
    if($filevalue['display_list'] == '1'){
        $defalut_fields[] = $filevalue['table_filed'];
        $defalut_fields_type[] = $filevalue['type'];
        if(in_array($filevalue['type'], (array)$type_array)){
            $field[$filevalue['table_filed']] =  dyanamicselectdata($table_name,$filevalue['table_filed']);
        }
    }
}

if(count((array)$_POST['displayattributes'])!='0'){
    $defalut_fields = $_POST['displayattributes'];
}
$option =  "COM_LIST_".strtoupper($table_name);
$page_details= toGetTableNameBaseApplicationPagesList('list_page_name', $option);
$insert = $page_details['insert_page_name'];
$view_page =  $page_details['view_page_name'];
$edit_insert =  $page_details['edit_page_name'];
$stausresult = ViewStatusData();
?>
<script type="text/javascript">
function displaystausdrop(id,table,status_id){
    $.ajax({
          url: "ajax.php",
          data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
          type: "POST",
          success:function(data){
            console.log(data);
              $("#dropdownlist_"+id).html(data);
          },
          error:function (){}
      });

  }
  function customFilterField(fieldname){
        var formdetails = $("#custom_fillter").serialize();
        console.log(formdetails);
  }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">

            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-custom " id="kt_page_sticky_card">
                        <div class="card-header">
                        <form class="kt-form" id="kt_form" method="post" action="" >
                            <div class="card-title row col-lg-12">


            <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>" >

                                <div class="col-lg-3"> <h5 class="card-label">List <?php echo ucwords($page_title); ?></h5> </div>
                                <div class="col-lg-7">
                                    <span class="f-w-600">Field Lists</span>
                                    <select aria-label="Displayattributes[]" class="form-select js-example-basic-multiple-limit_custom" name="displayattributes[]" multiple="multiple">
                                        <?php $jd='0'; foreach ($tablefiledslist as $keyvalue => $fieldvalue){
$lock = "";
if(in_array($keyvalue, (array)$defalut_fields) && $jd =='0'){
    $lock = 'locked="locked"';
    $jd++;
}
                                            ?>
                                            <option value="<?php echo $keyvalue; ?>"  <?php echo in_array($keyvalue, (array)$defalut_fields) ? 'selected' : ''; ?>   <?php echo $lock; ?> > <?php echo $fieldvalue; ?> </option>
                                        <?php  }    ?>
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <input type="hidden" id="filterby" name="filterby">
                                    <button id="kt_login_signin_submit" class="btn btn-primary mt-4 btn-shadow-hover font-weight-bold mr-2"  name="applyform" value="apply">Apply</button>
                                </div>

                            </div>
                            </form>
                            <div class="row">
                            <form id="custom_fillter" method="post"  >
                            <div class="row">

                        <?php foreach($field as $fieldskey=>$fieldsloop){ ?>
                            <div class="col-lg-4">
                                    <span class="f-w-600"><?php echo ucwords(str_replace("_"," ",($fieldskey))); ?></span>
                                    <select aria-label="Select option" class="form-select js-example-basic-multiple-limit_custom" id="filter_<?php echo $fieldskey; ?>"  name="f_<?php echo $fieldskey; ?>" onchange="customFilterField('<?php echo $fieldskey; ?>')" multiple="multiple">
                                        <?php foreach ($fieldsloop as $keyvalue1 => $fieldvalue1){
                                            ?>
                                            <option value="<?php echo $keyvalue1; ?>"  > <?php echo $fieldvalue1; ?> </option>
                                        <?php  }    ?>
                                    </select>
                                </div>
                        <?php } ?>
                            </div>
                            </form></div>
                        </div>

                        <!--end::Charts Widget 2-->
                    </div>

                </div>
            </div>


        <div class="card card-custom ">
            <div class="card-body">
                        <!--begin: Datatable -->
                         <div class="dt-responsive table-responsive">
        <table id="basic-btn" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">SL.No.</th>

                                    <?php foreach ($tablefiledslist as $keyvalue => $fieldvalue){
                                        if(in_array($keyvalue, (array)$defalut_fields)){
                                            ?>
                                            <th scope="col">
                                                <?php echo $fieldvalue; ?> </th>
                                            <?php } ?>
                                            <?php } ?>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $serialno = 1;
                                        foreach ($tabledetails['list'] as $resultval) {
                                            $rrid = deocdeId("id:".$resultval['id']);
                                            $edit =  "index.php?option=".$insert."&Rid=".$rrid;
                                            ?>
                                            <tr>
                                                <td><?php echo $serialno; ?></td>
                                                <?php
                                                    foreach ($tablefiledslist as $keyvalue => $fieldvalue){
                                                    if(in_array($keyvalue, (array)$defalut_fields)){
                                                      ?>

                                                            <?php if(in_array($tablefileddtype[$keyvalue], (array)$type_array)){
                                                                echo "<td>".$field[$keyvalue][$resultval[$keyvalue]]."</td>";

                                                            }elseif($keyvalue == 'current_status'){ ?>
                                                        <td>
                                                    <div class="btn-group">
                                                       <button onclick="displaystausdrop(<?php echo $resultval['id']; ?>,<?php echo "'".$table_name."'"; ?>,<?php echo $resultval['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".$stausresult[$resultval['current_status']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$resultval['current_status']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$resultval['current_status']]['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $stausresult[$resultval['current_status']]['name']; ?> </button>
                                                      <div class="dropdown-menu" id="dropdownlist_<?php echo $resultval['id']; ?>">

                                                      </div>
                                                  </div>
                                                    </td>
                                                <?php }elseif ($keyvalue =='company_id') { ?>

                                                     <td><?php echo $_SESSION['company'][$resultval[$keyvalue]]; ?></td>

                                            <?php    }else{
                                                        if($tablefileddtype[$keyvalue]=="number_type"){
                                                        ?>
                                                        <td style="text-align: right;"><?php echo customeummarmonyformate($resultval[$keyvalue]); ?></td>
    <?php
                                                        }elseif($tablefileddtype[$keyvalue] == 'date_pick'){ ?>
                                                <td><?php echo $resultval[$keyvalue] != "" ?fromDate($resultval[$keyvalue]):""; ?></td>
                                                    <?php    }else{
                                                            echo "<td>".$resultval[$keyvalue]."</td>";
                                                        }

                                                            } ?>

                                                    <?php } ?>
                                                    <?php } ?>
                                                    <td>
                                                            <a href="<?php echo $edit; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm"   title="Edit Datails" ><i class="fa fa-edit"></i></a>
                                                    </td>
                                                </tr>

                                                <?php $serialno++;
} ?>
                                            </tbody>
                                        </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable"  >
                    <div class="modal-content"  id="popup2">

                    </div>
                </div>
            </div>
