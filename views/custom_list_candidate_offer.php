<?php
$rid =  encodeId($_REQUEST['Rid']);
$rid = urldecode($rid);
$cand = explode(':', $rid);
$candidate_id = $cand['1'];
$diplay_image = "uploads/all";
$url1 = API."?mode=getcandidateofferdetails&rid=".$candidate_id;
$res = callAPI($url1);
$return  = json_decode($res,1);
$data  = $return['candidates'];
$candiddata = $return['candidates'];
$offer_details  = $return['offer_details'];
$jobcodedata  = $return['job_titles'];
$jobpaydata  = $return['job_grade_templates'];
$relatypedata  = $return['letters'];
$hrempdata  = $return['hrempdata'];
$salarydata  = $return['salary_template'];
$salarytmpdata  = $return['salary_template_components'];
$salarycatdata  = $return['salary_categories'];
$candhistrydata  = $return['candidate_offer_histories'];
$emailres  = $return['email_content'];
$admin_application_users = $return['admin_application_users'];
$rm_application_users = $return['rm_application_users'];
foreach ($offer_details as  $offer_detail) {
    if($offer_detail['parent_id']=='0'){
        $catdetails[$offer_detail['category_id']] = $offer_detail;
    }else{
        $subcatdetails[$offer_detail['parent_id']][] = $offer_detail;
    }
}
foreach ($salarycatdata as $cate) {
    $cat[$cate['id']] = $cate['name'];
}
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="d-flex flex-row">
            <?php  include_once 'candidate_side_nav.php'; ?>
            <?php
            $_SESSION['apply_js']['googlechart']=1;
            $send_pie ="";
            $sct = array('xyz:0','xyz:0','xyz:0','xyz:0','xyz:0','xyz:0','xyz:0');
            $m=0;
            foreach ($salarytmpdata as $salarytmp) {
                    $sct[$m]=$salarytmp['name'].":".round($salarytmp['yearly_amount']/10000);
                $m++;
}
                $send_pie = implode("||", (array)$sct);
                $ledata = $relatypedata['0'];
                $send_data['display_name'] = $data['display_name'];
                $send_data['address'] = $data['address'];
                $send_data['to_day'] = date('dS F, Y');
                $send_data['company_name'] = $_SESSION['company'][$_SESSION['company_id']];
                $send_data['job_title'] = $jobcodedata['job_title'] ;
                $send_data['reporting_date'] =  date('dS F, Y' ,strtotime($data['reporting _date']));
                $send_data['job_grade'] =  $jobgradedata['job_grade_name'];
                $send_data['amount'] = customeummarmonyformate($salarydata['company_cost_yearly']);
                $send_data['location'] =   '';
                $send_data['place'] =   '';
                ?>
                <script type="text/javascript">
                    var signaturePad;
                    var signBast64JPEGContent;
                    function printForm() {
                        var divToPrint =document.getElementById('printarea');
                        var newWin=window.open('','Print-Window');
                        newWin.document.open();
                        newWin.document.write('<html lang="en"><head><title>Candidate Offer</title></head><body onload="window.print()">'+divToPrint.outerHTML+'</body></html>');
                        newWin.document.close();
                    }
                    function signpopup(status,id){
                        jQuery.ajax({
                            url: "views/custom_insert_status_sign_popup.php",
                            data:'table=candidates&status='+status+'&rid='+id,
                            type: "POST",
                            success:function(data){
                                $('#popup1').html(data);
                                $("#kt_modal_KTDatatable_remote").modal("show");
                                var signPadCanvas = document.querySelector('#signPadCanvas');
                                var ratio =  Math.max(window.devicePixelRatio || 1, 1);
                                signPadCanvas.getContext("2d").scale(ratio, ratio);
                                signaturePad = new SignaturePad(signPadCanvas, {backgroundColor: 'rgb(218, 220, 220)'});
                            },
                            error:function (){}
                        });
                    }
                    function statuspopup(status,id){
                        jQuery.ajax({
                            url: "views/custom_insert_status_popup.php",
                            data:'table=candidates&status='+status+'&rid='+id,
                            type: "POST",
                            success:function(data){
                                $('#popup1').html(data);
                                $("#kt_modal_KTDatatable_remote").modal("show");
                            },
                            error:function (){}
                        });
                    }
                    function clarityquery(){
                        var candidate_id =  $('#candidate_id').val();
                        jQuery.ajax({
                            url: "views/custom_insert_query.php",
                            data:'table_name=candidate_info'+'&tran_no='+candidate_id+'&hr_connect='+ $('#hr_connect_id').val() +'&rid='+candidate_id,
                            type: "POST",
                            success:function(data){
                                $('#popup1').html(data);
                                $("#kt_modal_KTDatatable_remote").modal("show");
                            },
                            error:function (){}
                        });
                    }
                </script>
                <script type="text/javascript">
                    function dynamicinformationopup(id){
                        jQuery.ajax({
                            url: "views/custom_list_candidateoffer_popup.php",
                            data:'id='+id,
                            type: "POST",
                            success:function(data){
                                $('#popup1').html(data);
                                $("#kt_modal_KTDatatable_remote").modal("show");
                            },
                            error:function (){}
                        });
                    }
                </script>
                <style>
                    .btnwidth{
                        width: 30%;
                    }
                    .Congrats {
                        padding: 30px 105px;
                    }
                </style>
                <div class="card-custom mb-5 p-3 mt-3" style="width: 100%;">
                    <ul class="nav  md-tabs infisuite_tab">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab"  role="tab" aria-selected="false"   href="#join-candidate" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <span class="nav-icon"><i class="fa fa-user"></i> </span>&nbsp;
                                <span class="nav-text">Join Candidate</span>
                            </a>
                            <div class="slide"></div>
                        </li>
                        <?php if($_SESSION['config']['application_module']!=3){ ?>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab"  href="#offer-detail" role="tab" aria-selected="false"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <span class="nav-icon"><i class="fa fa-file"></i> </span>&nbsp;
                                    <span class="nav-text">Offer Detail</span>
                                </a>
                                <div class="slide"></div>
                            </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#additional-benefits" role="tab" aria-selected="false"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <span class="nav-icon"><i class="fa fa-cubes"></i></span>&nbsp;
                                <span class="nav-text">Additional Benefits</span>
                            </a>
                            <div class="slide"></div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#advantage" role="tab" aria-selected="false"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <span class="nav-icon"><i class=" ti-gift"></i></span>&nbsp;
                                <span class="nav-text">Advantages</span>
                            </a>
                            <div class="slide"></div>
                        </li>
                        <?php if($_SESSION['config']['application_module']!=3){ ?>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#offer-letter" role="tab" aria-selected="false"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <span class="nav-icon"><i class="fa fa-file"></i></span>&nbsp;
                                    <span class="nav-text">Offer Letter</span>
                                </a>
                                <div class="slide"></div>
                            </li>
                        <?php }
if($candiddata['current_status'] == ST_LOIACCEPTED){ ?>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#congratulation" role="tab" aria-selected="false"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <span class="nav-icon"><i class="fa fa-thumbs-up"></i></span>&nbsp;
                                    <span class="nav-text">Congratulation</span>
                                </a>
                                <div class="slide"></div>
                            </li>
                        <?php } ?>
                    </ul>
                    <input type="hidden" name="" id='candidate_id' value="<?php echo $candidate_id ?>">
                    <input type="hidden" name="" id='hr_connect_id' value="<?php echo $data['hr_connect']; ?>">
                    <div class="tab-content mt-5 join-candidate p-4" id="myTabContent">
                        <div class="tab-pane fade show active" id="join-candidate" role="tabpanel" aria-labelledby="kt_tab_pane_2" >
                            <div class="row">
                                <?php if($catdetails['1']['header']!=''){ ?>
                                    <div class="col-lg-12 text-center">
                                        <h2 class=""><?php echo $catdetails['1']['header']; ?></h2>
                                        <?php if($catdetails['1']['sub_header']!=''){ ?>
                                            <p><?php echo $catdetails['1']['sub_header']; ?></p>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <div class="col-lg-8 p-l-50 p-r-50">
                                    <div class="bg-join Congrats">
                                        <h4 class="text-primary text-capitalize"><span>Dear</span> <?php echo $data['display_name']; ?></h4>
                                        <p class="mt-5">
                                            <?php echo $catdetails['1']['content']; ?>
                                        </p>
                                        <div class="dropdown dropdown-inline float-right" data-toggle="tooltip" title="Memebr Profile" data-placement="top">
                                            <a href="#" class="d-flex align-items-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                <div class="d-flex flex-column text-right">
                                                    <span class="text-muted font-weight-bold mr-3">HR Manager</span>
                                                    <span class="font-weight-bold mr-3"><?php echo $_SESSION['users'][$data['hr_connect']]; ?></span>
                                                </div>
                                                <div class="symbol symbol-circle symbol-50 mr-3  m-l-10">
                                                    <img src="<?php echo $diplay_image."/".$hrempdata['image']; ?>" class="img-radius img-50 align-top m-r-15  mt-3" alt="Attachment Preview">
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <img src="<?php echo $diplay_image."/".$catdetails['1']['image']; ?>" class="w-100" alt="Attachment Preview">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="offer-detail" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                            <div class="row">
                                <?php if($catdetails['2']['header']!=''){ ?>
                                    <div class="col-lg-12 text-center">
                                        <h3 class="f-w-700"><?php echo $catdetails['2']['header']; ?></h3>
                                        <?php if($catdetails['2']['sub_header']!=''){ ?>
                                            <p><?php echo $catdetails['2']['sub_header']; ?></p>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <div class="col-lg-5">
                                    <div class="ctc">
                                        <div class="row">
                                            <div class="col-lg-2"><i class="ti-user icon-40-blue"></i></div>
                                            <div class="col-lg-10">
                                                <h4>Designation</h4>
                                                <span><?php echo $jobcodedata['job_title']; ?></span>
                                                <p><?php echo $_SESSION['config']['name'];?></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2"><i class="fas fa-rupee-sign icon-40-blue"></i></div>
                                            <div class="col-lg-10">
                                                <h4>Annual CTC</h4>
                                                <span>
                                                    <?php echo customeummarmonyformate($salarydata['ctc_yearly']); ?>
                                                </span>
                                                <p>View your offer letter for a details</p>
                                            </div>
                                        </div>
                                        <ul class="nav md-tabs mb-5 mt-10 graph-join infisuite_tab">
                                            <li class="nav-item btnwidth">
                                                <a class="nav-link active " data-bs-toggle="tab" href="#graph" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                    <span class="nav-text">Graph</span>
                                                </a>
                                            </li>
                                            <li class="nav-item btnwidth">
                                                <a class="nav-link " data-bs-toggle="tab" href="#join-table" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                    <span class="nav-text">Table</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="graph">
                                        <div class="tab-content" id="myTabContent_2">
                                            <div class="tab-pane fade show active" id="graph" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                                                <input type="hidden" name="" id='pieee' value="<?php echo $send_pie; ?>">
                                                <div id="chart_Donut" style="width: 100%; height: 300px;"></div>
                                            </div>
                                            <div class="tab-pane fade show" id="join-table" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                                                    <div class="card card-custom card-stretch gutter-b">
                                                        <div class="card-header border-0">
                                                            <h4 class="card-title align-items-start flex-column text-dark">
                                                                Compensation Structure
                                                                <span class="text-muted mt-3 font-weight-bold font-size-sm"></span>
                                                            </h4>
                                                        </div>
                                                        <div class="card-body py-0 mt-n3">
                                                            <div class="table-responsive">
                                                                <table class="table table-borderless table-vertical-center">
                                                                    <thead>
                                                                        <tr class="bg-primary text-white">
                                                                            <th scope="col" class="p-2 font-weight-bolder font-size-h6 " style="min-width: 150px">COMPONENTS</th>
                                                                            <th scope="col" class="p-2 font-weight-bolder font-size-h6 text-right" style="min-width: 70px">MONTHLY CTC</th>
                                                                            <th scope="col" class="p-2 font-weight-bolder font-size-h6 text-right" style="min-width: 70px">YEARLY CTC</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php  $toatal_month=0;$toatal_year=0;
                                                                        foreach ($salarytmpdata as $salarytmp){
                                                                                $toatal_month = $toatal_month+$salarytmp['monthly_amount'];
                                                                                $toatal_year = $toatal_year+$salarytmp['yearly_amount'];
                                                                                ?>
                                                                                <tr >
                                                                                    <td class="p-2">
                                                                                        <a href="#" class="text-dark font-weight-bolder text-hover-primary mb-1 text-whitefont-size-lg"><?php echo strtoupper($salarytmp['name']);  ?></a>
                                                                                    </td>
                                                                                    <td class="text-right p-2">
                                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg"><?php echo customeummarmonyformate($salarytmp['monthly_amount']);  ?></span>
                                                                                    </td>
                                                                                    <td class="text-right p-2">
                                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg"><?php echo customeummarmonyformate($salarytmp['yearly_amount']);  ?></span>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php } //}
                                                                            ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="additional-benefits" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                                <div class="row">
                                    <?php if($catdetails['3']['header']!=''){ ?>
                                        <div class="col-lg-12 text-center">
                                            <h2 class=""><?php echo $catdetails['3']['header']; ?></h2>
                                            <?php if($catdetails['3']['sub_header']!=''){ ?>
                                                <p><?php echo $catdetails['3']['sub_header']; ?></p>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <?php  if(count((array)$subcatdetails['3']) >0){
                                                foreach ($subcatdetails['3'] as  $subcat3){ ?>
                                                    <div class="col-md-4">
                                                        <div class="card card-custom card-shadowless">
                                                            <div class="card-body p-0">
                                                                <div class="overlay">
                                                                    <div class="overlay-wrapper rounded bg-light text-center">
                                                                        <img src="<?php echo $diplay_image."/".$subcat3['image']; ?>" alt="" class="mw-100 w-200px img-120">
                                                                    </div>
                                                                    <div class="text-center mt-2">
                                                                        <button type="button" onclick="dynamicinformationopup(<?php echo $subcat3['id']; ?>)"  class="btn font-weight-bolder btn-sm btn-primary mr-2">Quick View</button>
                                                                    </div>
                                                                </div>
                                                                <div class="text-center mt-5 mb-md-0 mb-lg-5 mb-md-0 mb-lg-5 mb-lg-0 mb-5 d-flex flex-column">
                                                                    <a href="#" class="font-size-h5 font-weight-bolder text-dark-75 text-hover-primary mb-1">
                                                                        <?php echo  $subcat3['header']; ?></a>
                                                                        <span class="font-size-lg"><?php echo  $subcat3['sub_header']; ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php } else { echo '<div class="text-center"><h4 class="text-info"><i class="fa fa-warning"></i> No Additional Benefits</h4></div>';
} ?>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mt-6">
                                                <img src="<?php echo $diplay_image."/".$catdetails['3']['image']; ?>" class="w-100" alt="Attachment Preview">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="advantage" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                                        <div class="row">
                                            <?php $cat_pid= $catdetails['4']['id']; if($catdetails['4']['header']!=''){ ?>
                                                <div class="col-lg-12 text-center">
                                                    <h3 class="f-w-700"><?php echo $catdetails['4']['header']; ?></h3>
                                                    <?php if($catdetails['4']['sub_header']!=''){ ?>
                                                        <p><?php echo $catdetails['4']['sub_header']; ?></p>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                            <div class="col-lg-3">
                                                <ul class="nav md-tabs mb-5 mt-10 graph-join infisuite_tab" style="    width: 0%;">
                                                    <?php $k=0; foreach ($subcatdetails[$cat_pid] as  $subcat4) { ?>
                                                        <li class="nav-item mb-4">
                                                            <a class="nav-link <?php if($k==0){ echo "active";
} ?> btn-lg" data-bs-toggle="tab" href="#advantage-<?php echo $subcat4['id'];  ?>">
                                                                <?php echo $subcat4['header']; ?>
                                                            </a>
                                                        </li>
                                                        <?php $k=1;
} ?>
                                                    </ul>
                                                </div>
                                                <div class="col-lg-9">
                                                    <div class="tab-content mt-5" id="myTabContent_3">
                                                        <?php $k=0; foreach ($subcatdetails[$cat_pid] as  $subcat4) { ?>
                                                            <div class="tab-pane fade show <?php if($k==0){ echo "active";
} ?>" id="advantage-<?php echo $subcat4['id'];  ?>" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <h3 class="f-w-700"><?php echo $subcat4['header']; ?></h3>
                                                                        <span><?php echo $subcat4['sub_header']; ?></span>

                                                                        <p class="mt-5"><?php echo $subcat4['content']; ?></p>

                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <img class="w-100" src="<?php echo $diplay_image."/".$subcat4['image']; ?>" alt="Attachment Preview">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php $k=1;
} ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="offer-letter" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="card card-custom card-stretch" id="kt_page_stretched_card">
                                                            <div class="card-header">
                                                                <h5>Offer</h5>
                                                                <div class="card-header-right">
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="card-scroll" id="printarea">
                                                                    <?php
                                                                    include_once 'custom_view_candidate_offer_latter_pdf.php'; ?>
                                                                    <?php if($_SESSION['user_type'] != '4'){ ?>
                                                                        <div class="invoice-btn-section clearfix d-print-none" style="margin-top:5px;">
                                                                            <button class="btn waves-effect waves-light btn-grd-success" onclick="printDiv('printDiv');">
                                                                                <i class="fa fa-print"></i> &nbsp Print Offer Latter
                                                                            </button>
                                                                            <button class="btn waves-effect waves-light btn-grd-info"  onclick="convertHTMLToPDF();">
                                                                                <i class="fa fa-download"></i> &nbsp Download Offer Latter
                                                                            </button>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mt-10">
                                                        <?php $cat_pid= $catdetails['5']['id']; if($catdetails['5']['header']!=''){ ?>
                                                            <div class="col-lg-12 text-center">
                                                                <h2 class=""><?php echo $catdetails['5']['header']; ?></h2>
                                                                <?php if($catdetails['5']['sub_header']!=''){ ?>
                                                                    <p><?php echo $catdetails['5']['sub_header']; ?></p>
                                                                <?php } ?>
                                                            </div>
                                                        <?php }      ?>
                                                        <div class="card card-custom wave wave-animate-slow mb-8 mb-lg-0 offer-letter">
                                                            <?php if($candiddata['current_status']== ST_APPROVED){ ?>
                                                                <div class="ribbon ribbon-top-right"><span class="color-info">Accepted</span></div>
                                                            <?php }elseif($candiddata['current_status']== ST_REJECTED){ ?>
                                                                <div class="ribbon ribbon-top-right"><span class="color-danger">Decline</span></div>
                                                            <?php } ?>
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-center p-3">
                                                                    <div class="mr-6">
                                                                        <span class="far fa-bell f-40 mr-15"></span>
                                                                    </div>
                                                                    <div class=" flex-column">
                                                                        <a href="#" class="text-dark text-hover-primary f-w-700 font-weight-bold font-size-h4 mb-3">
                                                                            Offer Letter Validity
                                                                        </a>
                                                                        <div class="text-dark-75"><?php echo date('M d, Y',strtotime($data['offer_validity'])); ?></div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex align-items-center p-3">
                                                                    <div class="mr-6">
                                                                        <span class="far fa-calendar-alt f-40 mr-15"></span>
                                                                    </div>
                                                                    <div class=" flex-column">
                                                                        <a href="#" class="text-dark text-hover-primary f-w-700 font-weight-bold font-size-h4 mb-3">Join Date
                                                                        </a>
                                                                        <div class="text-dark-75">On or before <?php echo date('M d, Y',strtotime($data['reporting_date'])); ?></div>
                                                                    </div>
                                                                </div>
                                                                <?php if($candiddata['current_status']==ST_LOIACCEPTED || $candiddata['current_status']==ST_LOIREJECTED){ ?>
                                                                    <div class="d-flex align-items-center p-3">
                                                                        <div class="mr-6 f-40">
                                                                            <?php if($candiddata['current_status']==ST_LOIACCEPTED){
                                                                                ?>
                                                                                <span class="fa fa-thumbs-up  mr-15"></span>
                                                                            <?php } ?>
                                                                            <?php if($candiddata['current_status']==ST_LOIREJECTED){
                                                                                ?>
                                                                                <span class="fa fa-thumbs-down mr-15"></span>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                        <div class=" flex-column">
                                                                            <a href="#" class="text-dark text-hover-primary f-w-700 font-weight-bold font-size-h4 mb-3"><?php if($candiddata['current_status']==ST_LOIACCEPTED){ echo "Offer Accepted";
}elseif($candiddata['current_status']== ST_LOIREJECTED){  echo "Offer Decline";
}?>
                                                                        </a>
                                                                        <div class="text-dark-75"><?php echo date('M d, Y',strtotime($candhistrydata['status_date'])); ?></div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 text-center mt-3">
                                                        <?php
                                                        if($candiddata['current_status'] == ST_LOIACCEPTED || $candiddata['current_status'] == ST_LOIREJECTED){
                                                            $notauth = 'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$_SESSION['error_message']['9002'].'"';
                                                            $notauthclass = "btn-disabled disabled";
                                                        }elseif($data['user_id'] != $_SESSION['userid']){
                                                            $notauth = 'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$_SESSION['error_message']['9002'].'"';
                                                            $notauthclass = "btn-disabled disabled";
                                                            $errormsg = $_SESSION['error_message']['9002']." Accept/Decline";

                                                        } ?>
                                         <span class="text-danger d-block"><?php echo $errormsg; ?> </span>
                                         <button class="btn waves-effect waves-light btn-grd-primary  mt-2  <?php echo $notauthclass; ?>" type="button"
                                             onclick="signpopup(<?php echo ST_APPROVED; ?>,<?php echo $candidate_id; ?>)"  <?php echo $notauth; ?>>
                                             Accept
                                         </button>
                                         <button class="btn waves-effect waves-light btn-grd-danger  mt-2 <?php echo $notauthclass; ?>" type="button"   onclick="statuspopup(<?php echo ST_LOIREJECTED; ?>, <?php echo $candidate_id; ?>)" <?php echo $notauth; ?>>
                                             Decline
                                         </button>
                                         <button class="btn waves-effect waves-light btn-grd-warning  mt-2 " type="button" onclick="clarityquery()" >
                                             Query
                                         </button>
                                     </div>
                                     <div class="row">
                                         <?php
                                         $ddd['candidate_id']  = $candidate_id;
                                         ?>
                                     </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="congratulation" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <div class="Congrats">
                                            <h2>Congratulations <?php echo $data['display_name']; ?> </h2>
                                            <h3>Welcome!</h3>
                                            <p> We choose our new team members carefully and we’re proud to welcome you, with all of your talents and ideas and faults.</p><p>     Yes, faults — making mistakes is an important part of growth, and we hope you’ll never worry about trying to be perfect on the job.</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <img src="uploads/all/congrats.jpg" style="width:500px;" alt="Attachment Preview">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog"  tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>
<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content"  id="popup1">
        </div>
    </div>
</div>
<?php if($_POST['ajaxpage'] == 'Y'){ ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js" integrity="sha384-Q4nTc23a1YNtnl17XDjJkYn/j5Ksb7rsGG1NTcIxbz6sTGfGXZJ8WdvzALeeuafr" crossorigin="anonymous"></script>
    <script type="text/javascript" src="assets/pages/chart/google/js/custom-google-chart.js"></script>
    <?php } ?>
