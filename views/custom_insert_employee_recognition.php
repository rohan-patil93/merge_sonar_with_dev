<?php
$_SESSION['apply_js']['multiselest'] = 1;
$rids =  encodeId($_GET['Rid']);
$rid = urldecode($rids);
$cand = explode(':', $rid);
$rowid = $cand['1'];
$cur_year = date('Y');
$cur_month = date('m');
if($rowid!=''){
    $data12["where"] = array('id'=>$rowid);
    $datw12 = urlencode(json_encode($data12));
    $curl12 = API."?mode=gettabledetailswithconditions&table=".TBL_EMPLOYEE_RECOGNITIONS."&data=".$datw12;
    $cudata12 = callAPI($curl12);
    $emp_recognition = json_decode($cudata12,1);
    $emp_recognition_data = $emp_recognition[0];
}
if($_GET['PID']!=""){
    $monyr = urldecode($_GET['PID']);
    $exploded_monyr = explode('-', $monyr);
    $cur_year = $exploded_monyr[0];
    $cur_month = $exploded_monyr[1];
}
$vurl = API."?mode=getrecognitionsettings&table=".TBL_EMPLOYEE_RECOGNITIONS."&userid=".$_SESSION['userid']."&emp_id=".$_SESSION['emp_id']."&month=".$cur_month."&year=".$cur_year;
$recog_data = callAPI($vurl);
$recogdata = json_decode($recog_data,1);
$recog_types = $recogdata['recognition_types'];
$emplist = $recogdata['employees'];
$users = $recogdata['users'];
$badges = $recogdata['badges'];
$recognition_tablist = $recogdata['tabslist'];
$totalreceived = count((array)$recognition_tablist[0]);
$totalsent = count((array)$recognition_tablist[1]);
$totalemp = count((array)$recognition_tablist[2]);
$tabcontent = array('0'=>array('Received','recognition_from'),'1'=>array('Sent','recognition_to'),'2'=>array('Employee','recognition_to'));
$recognition_type = "Recognition";
$File_Uploads_Path = "uploads/all/";
?>

<script type="text/javascript">

function updaterolemodal(){
   $(".updaterolemodal").modal("show");
}

    function selectbadge(badgeid) {
        document.getElementById('badge_id').value = badgeid;
    }

    function selectmonth(monyr) {
        var url = '<?php echo $_GET['option'];?>';
        pid = encodeURI(monyr);
        window.location.href = "index.php?option="+url+"&PID="+pid;
    }

    function showrecognitioncard(recogids) {
        jQuery.ajax({
            url: "views/custom_view_employee_recognition_popup.php",
            data:'recogid='+recogids,
            type: "GET",
            success:function(data){
                $('#popup5').html(data);
                 $(".updaterolemodal").modal("show");
            },
            error:function (){}
        });
    }

    function recognitionpopup(){

        var rowid = document.getElementById('row_id').value;
        var selected = [];
        for (var option of document.getElementById('kt_select2_43').options)
        {
            if (option.selected) {
                selected.push(option.value);
            }
        }
        var comments = document.getElementById('comments').value;
        var badgeid = document.getElementById('badge_id').value;
        var badgeid = $('#badge_id').val();
        var company_announcement_id = document.getElementById('company_announcement_id').value==''?'0':document.getElementById('company_announcement_id').value;
        var display_in_announcement = document.getElementById('display_in_announcement').checked ? '0' : '1';

        if(selected.length) {
            jQuery.ajax({
                url: "views/custom_insert_employee_recognition_popup.php",
                data:'comments='+comments+'&badgeid='+badgeid+'&recognized_employees='+selected+'&display_in_announcement='+display_in_announcement+'&company_announcement_id='+company_announcement_id+"&rowid="+rowid,
                type: "GET",
                success:function(data){
                    $('#popup5').html(data);
                     $(".updaterolemodal").modal("show");
                },
                error:function (){}
            });
        }
    }

    function redirecttolist() {
        $(".updaterolemodal").modal("hide");
        location.reload();

    }

    function displaystausdrop(id,table,status_id){
   $.ajax({
       url: "ajax.php",
       data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
       type: "POST",
       success:function(data){
           console.log(data);
           $("#dropdownlist").html(data);
       },
       error:function (){}
   });

}

function statuspopup(status,id){
   var table_name = 'employee_recognitions';
   jQuery.ajax({
       url: "views/custom_insert_status_popup.php",
       data:'table='+table_name+'&status='+status+'&rid='+id,
       type: "POST",
       success:function(data){
           $('#popup5').html(data);
            $(".updaterolemodal").modal("show");
       },
       error:function (){}
   });
}
</script>
<style type="text/css">
/* HIDE RADIO */
[type=radio] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

/* IMAGE STYLES */
[type=radio] + img {
    cursor: pointer;
}

/* CHECKED STYLES */
[type=radio]:checked + img {
    -webkit-box-shadow: 0 1rem 2rem 1rem rgba(0, 0, 0, .1)!important;
    box-shadow: 0 1rem 2rem 1rem rgba(0, 0, 0, .50)!important;

}
</style>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-5">
                <div class="card card-custom " id="kt_page_sticky_card">


                     <div class="card-header table-card-header">
                      <h5> Employee Recognition</h5>
                    <div class="card-header-right table-card-header ">
                         <?php
                                $table_name = 'employee_recognitions';
                                if($rowid!="") { $nmm= ViewStatusValue($emp_recognition_data['current_status'],$table_name);  ?>
                                <div class="btn-group">
                                    <button onclick="displaystausdrop(<?php echo $rowid; ?>,<?php echo "'".$table_name."'"; ?>,<?php echo $emp_recognition_data['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#". $nmm['color']; ?>;    background:  <?php echo "#".strval($nmm['background_color'] ); ?>; border-color: <?php echo "#".strval($nmm['background_color']); ?>;" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $nmm['name']; ?> </button>
                                    <div class="dropdown-menu" id="dropdownlist_2">
                                    </div>
                                </div>
                                <?php } ?> &nbsp;
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                              <i class="icofont icofont-reply"></i>
                              Back
                        </a>
                      </div>

                    </div>
                    <div class="card-body mt-3 ">
                        <div class="col-lg-12">
                            <input type="hidden" name="row_id" id="row_id" value="<?php echo $_GET['Rid'];?>" >
                            <input type="hidden" name="company_announcement_id" id="company_announcement_id" value="<?php echo $emp_recognition_data['company_announcement_id']=='0' ? '0' : $emp_recognition_data['company_announcement_id'];?>">

                            <div class="form-group row w-full">
                                <span class="">Whom do you want to recognize ? </span>
                                <div class="">
                                    <select aria-label="Recognition To[]" class="form-control js-example-basic-multiple" id="kt_select2_43" name="recognition_to[]"  multiple="multiple">
                                        <?php foreach($emplist as $modekey=>$modevalue) {
                                                if(($modevalue['date_of_leaving']!="" && strtotime($modevalue['date_of_leaving'])  > strtotime(date('Y-m-d')))|| $modevalue['date_of_leaving']=="" ){
                                            ?>
                                            <option value="<?php echo $modekey;?>" <?php echo $emp_recognition_data['recognition_to']==$modekey ? 'selected':'';?> ><?php echo $modevalue['display_name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <span class="">Appreciate Message</span>
                                <div class="">
                                    <input type="hidden" name="badge_id" value="<?php echo $emp_recognition_data['badge_id'];?>">
                                    <textarea aria-label="Comments" type="text" class="form-control" id="comments" name="comments" rows='6'><?php echo $emp_recognition_data['comments'];?></textarea>
                                </div>
                            </div>
                            <input type="hidden" id="display_in_announcement_2" value="">
                            <div class="form-group">
                                <div class="checkbox-inline">


                                    <?php
                                foreach ($badges as $key => $value) {
                                        ?>
                                        <label class="symbol symbol-circle symbol-60 mb-5">
                                            <input aria-label="Badges" type="radio" name="badges" id="badge_id" onchange="selectbadge(this.value)" value="<?php echo $value['id'];?>" <?php echo $emp_recognition_data['badge_id']==$value['id']?'checked':'';?> >
                                            <img src="uploads/all/<?php echo $value['image'];?>" class="img-radius img-50 align-top " alt="Attachment Preview">
                                        </label>

                                        <?php
                                    } ?>

                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <button type="button" id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" onclick="recognitionpopup()" value="exit"><i class="la la-check"></i>Preview Recognition</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 border-0 j-pro j-content">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-3">
                                <input aria-label="Month Year Cal" class="form-control r-date" type="month" name="month_year_cal" onchange="selectmonth(this.value);" value="<?php echo $cur_year."-".$cur_month?>"  id="att_month_year" />
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-12 mb-4">
                                <ul class="nav md-tabs infisuite_tab">
                                    <li class="nav-item" data-bs-toggle="tooltip" data-placement="top" title="Received">
                                        <a class="nav-link active" id="tab0" data-bs-toggle="tab" href="#tablink0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                            <span class="nav-text mt-0"> <i class="fa fa-certificate"></i> <!-- Received -->
                                            <span class="label label-md label-white ml-2 bg-primary mt-0 d-inline-block"><?php echo $totalreceived;  ?></span>
                                            </span>
                                        </a>
                                         <div class="slide"></div>
                                    </li>
                                    <li class="nav-item" data-bs-toggle="tooltip" data-placement="top" title="Recognized">
                                        <a class="nav-link" id="tab1" data-bs-toggle="tab" href="#tablink1" aria-controls="taba1" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                <span class="nav-text mt-0"><i class="fa fa-thumbs-up"></i> <!-- Recognized -->
                                                <span class="label label-md label-white ml-2 bg-primary mt-0 d-inline-block"><?php echo $totalsent;  ?></span></span>
                                        </a>
                                        <div class="slide"></div>
                                    </li>
                                    <li class="nav-item" data-bs-toggle="tooltip" data-placement="bottom" title="All Recognitions" >
                                        <a class="nav-link " id="tab2" data-bs-toggle="tab" href="#tablink2" aria-controls="taba2" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                            <span class="nav-text mt-0"><i class="fa fa-users"></i> <!-- Recognized -->
                                                <span class="label label-md label-white ml-2 bg-primary mt-0 d-inline-block"><?php echo $totalemp;  ?></span></span>

                                        </a>
                                        <div class="slide"></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content" id="myTabContent4">
                            <?php foreach ($recognition_tablist as $tabindex => $tabvalue) {
                                ?>
                            <div class="tab-pane fade <?php echo $tabindex==0 ? 'show active' : ''?>" id="tablink<?php echo $tabindex; ?>" role="tabpanel" aria-labelledby="tab<?php echo $tabindex; ?>">
                                <div class="newcrds card card-custom col-lg-12">
                                    <div class="card-body">
                                        <!--begin: Datatable -->
                                        <?php if(count((array)$tabvalue) !=0){ ?>
                                        <table class="table table-hover" >

                                            <thead>
                                                <tr>
                                                    <th scope="col" style="width: 5%;">#</th>
                                                    <?php if($tabindex == '0' || $tabindex == '2'){ ?>
                                                    <th scope="col" style="width: 25%;">Recognized By</th>
                                                <?php }
                                                if($tabindex == '1' || $tabindex == '2'){ ?>
                                                    <th scope="col" style="width: 25%;">Recognized</th>
                                                <?php } ?>
                                                    <th scope="col" style="width: 25%;">Badges </th>
                                                    <th scope="col" style="width: 20%;">Month </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $serialno = 0;
                                                foreach ($tabvalue as $recvkey => $detailvalue) {
                                                    $serialno++;
                                                    $recognition_from_user_id = $detailvalue['recognition_from'];
                                                    $userdetail = $users[$recognition_from_user_id];
                                                    $recognition_to_emp_id = $detailvalue['recognition_to'];
                                                    $empdetail = $emplist[$recognition_to_emp_id];
                                                    $badgesdetail = $badges[$detailvalue['badge_id']];

                                                    ?>
                                                    <tr onclick="showrecognitioncard('<?php echo $detailvalue['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Veiw Bandage" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                        <td class="p-2  pt-3"> <?php echo $serialno; ?> </td>
                                                        <?php if($tabindex == '0' || $tabindex == '2'){ ?>
                                                        <td class="p-2">
                                                            <div class="align-middle">
                                                            <?php if($userdetail['image'] !=""){?>
                                                                <img src="uploads/all/<?php echo $userdetail['image']; ?>"  class="img-radius img-40 align-top m-r-5" style="display: inline-block;" alt="User Profile">
                                                            <?php } else { ?>
                                                                <span  alt="user" class="img-radius img-40 align-top m-r-5 custom_txt_round pt-1 f-12 text-center" style="display: inline-block;"><?php echo initials($userdetail['display_name']); ?></span>
                                                            <?php } ?>
                                                                <div class="d-inline-block width73 text-primary">
                                                                    <p class="text-muted m-b-0 mt-2"><?php echo $userdetail['display_name']; ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <?php }
                                                        if($tabindex == '1' || $tabindex == '2'){  ?>
                                                        <td class="p-2">
                                                            <div class="align-middle">
                                                            <?php if($empdetail['image'] !=""){?>
                                                                <img src="uploads/all/<?php echo $empdetail['image']; ?>"  class="img-radius img-40 align-top m-r-5" style="display: inline-block;" alt="Attachment Preview">
                                                            <?php } else { ?>
                                                                <span  alt="user" class="img-radius img-40 align-top m-r-5 custom_txt_round pt-1 f-12 text-center" style="display: inline-block;"><?php echo initials($empdetail['display_name']); ?></span>
                                                            <?php } ?>
                                                                <div class="d-inline-block width73 text-primary">
                                                                    <p class="text-muted m-b-0 mt-2"><?php echo $empdetail['display_name']; ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <?php } ?>
                                                        <td class="p-2">
                                                            <div class="align-middle" >
                                                                <img src="uploads/all/<?php echo $badgesdetail['image']; ?>"  class="img-radius img-40 align-top m-r-5" style="display: inline-block;" alt="Attachment Preview">
                                                                <div class="d-inline-block width73 text-primary" >
                                                                    <p class="text-muted m-b-0 mt-2"><?php echo $badgesdetail['badge_name']; ?></p>
                                                                </div>
                                                            </div>

                                                            </td>
                                                            <td class="p-2  pt-3"> <?php echo date('M, Y', strtotime($detailvalue['created_date'])); ?> </td>
                                                        </tr>
                                                    <?php } ?>
                                            </tbody>
                                        </table>
                                        <?php }else{ ?>
                                        <div class="accordion-desc custom-scroll p-0 ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active" style="height: 400px;" id="ui-id-2" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false">
                                       <div class="card-px text-center ">
                                          <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Badge</p>
                                          <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                          </div>
                                       </div>
                                    </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="kt_modal_KTDatatable_remote1 " class="modal fade updaterolemodal" role="dialog" >
    <div class="modal-dialog  " id="modalsize">
        <div class="modal-content "  id="popup5">

        </div>
    </div>
</div>

