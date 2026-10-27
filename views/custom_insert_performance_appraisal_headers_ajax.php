<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
if(isset($_POST['new_row']) && $_POST['new_row'] =="Y"){
    $mk = $_POST['rowCount'];
    $idd = "deletrow_".$_POST['rid'].$mk;
    $deletfun = "deletepahrow('".$idd."')";
    $html_page = '<tr class="'.$idd.'"><td class="p-2"><input aria-label="Field Name[" type="text" class="form-control text-capitalize inputtpehead" name="field_name['.$mk.']" value="'.$sub_headerres['field_name'].'" data-id="'.$_POST['rid'].$mk.'" "/></td><td class="p-2"><select aria-label="Type[" class="form-control form-select" name="type['.$mk.']" id="type_'.$_POST['rid'].$mk.'" required="" onchange="changetype('.$_POST['rid'].$mk.')"><option value="T" >Text</option><option value="S" >Select</option></select></td><td class="p-2  text-center"><div class="checkbox-fade fade-in-primary m-0"><label class="form-label"><input aria-label="Field Employee[" type="checkbox" value="Y" name="field_employee['.$mk.']" ><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></td><td class="p-2  text-center"><div class="checkbox-fade fade-in-primary m-0"><label class="form-label"><input aria-label="Field Ro[" type="checkbox" value="Y" name="field_ro['.$mk.']" ><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></td><td class="p-2  text-center"><div class="checkbox-fade fade-in-primary m-0"><label class="form-label"><input aria-label="Field Hod[" type="checkbox" value="Y" name="field_hod['.$mk.']" ><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></td><td onclick="'.$deletfun.'" role="button" tabindex="0" onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()"><button type="button" class="btn p-0 border-0 bg-transparent fa fa-trash text-danger" tabindex="0" onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()"></button></td></tr>';
    echo $html_page;
}elseif(isset($_POST['rid'])){
    $url = API."?mode=getperformanaceappraiserheader&assessment_year=".$_POST['assessment_year']."&appraisal_id=".$_POST['appraisal_id']."&Rid=".$_POST['rid'];
    $response = callAPI($url);
    $kipandkradetails = json_decode($response,1);
    $paheaders  = $kipandkradetails['performance_appraisal_headers'];

    $header_display_setting = json_decode($paheaders['header_display_setting'],1);
    $sub_header_name = json_decode($paheaders['sub_header_name'],1);
    $emp_check = $header_display_setting['EMP'] =='Y'?'Checked':'';
    $RO_check = $header_display_setting['RO'] =='Y'?'Checked':'';
    $HOD_check = $header_display_setting['HOD'] =='Y'?'Checked':'';
    $sub_header_addmore = $paheaders['sub_header_addmore'] =='0'?'Checked':'';
    $dynamic_page = $paheaders['dynamic_page'] =='0'?'Checked':'';
    $tableshow = $paheaders['dynamic_page'] =='0'?'style="display:none"':'';
    $pageshoq = $paheaders['dynamic_page'] =='0'?'':'style="display:none"';
    $html_page = '<input type="hidden" name="pah_id" value="'.$_POST['rid'].'">
    <div class="form-group"><span class="form-label">Header Name <span class="text-danger">*</span></span><input aria-label="Header Name" type="text" class="form-control text-capitalize header_name_text" name="header_name" value="'.$paheaders['header_name'].'" data-id="'.$_POST['rid'].'"/></div><div class="form-group"><span class="form-label">header Comment</span><textarea aria-label="Header Comment" class="form-control header_comment_text" name="header_comment" data-id="'.$_POST['rid'].'">'.$paheaders['header_comment'].'</textarea></div><div class="form-group"><span class="form-label">Dynamic Page</span></br><input aria-label="Dynamic Page" class="js-multidynamic'.$_POST['rid'].'" type="checkbox" value="0" name="dynamic_page" onchange="dynamicPage('.$_POST['rid'].')" id="dynamic_page_check" '.$dynamic_page.'/></div><div class="form-group pagenameshow" '.$pageshoq.'><span class="form-label">Page Name</span><input aria-label="Page Name" type="text" class="form-control" name="page_name" value="'.$paheaders['page_name'].'"/></div>';


    $html_page .= '<div class="form-group dynamicpageshow" '.$tableshow.'><span class="form-label">Sub Header Name</span><table class="table table-bordered"><thead><tr><th scope="col" class="p-2 f-14" style="width: 40%;">Heading</th><th scope="col" class="p-2 f-14" style="width: 30%;">Type</th><th scope="col" class="p-2 f-14" style="width: 10%;">Emp</th><th scope="col" class="p-2 f-14" style="width: 10%;">RO</th><th scope="col" class="p-2 f-14" style="width: 10%;">HOD</th><th scope="col" class="p-2 f-14" style="width: 10%;"></th></tr></thead><tbody id="subheadertable">';
    $mk=1;    foreach($sub_header_name as $sub_headerres){
        $emp_check_teb = $sub_headerres['EMP'] =='Y'?'Checked':'';
        $RO_check_teb = $sub_headerres['RO'] =='Y'?'Checked':'';
        $HOD_check_teb = $sub_headerres['HOD'] =='Y'?'Checked':'';
        $texttype = $sub_headerres["type"] =="T"?"selected":"";
        $selecttype = $sub_headerres["type"] =="S"?"selected":"";
        $idd = "deletrow_".$_POST['rid'].$mk;
        $deletfun = "deletepahrow('".$idd."')";
        $html_page .= '<tr  class="'.$idd.'"><td class="p-2"><input aria-label="Field Name[" type="text" class="form-control text-capitalize inputtpehead" name="field_name['.$mk.']" value="'.$sub_headerres['field_name'].'" data-id="'.$_POST['rid'].$mk.'" "/></td><td class="p-2"><select aria-label="Type[" class="form-control form-select" name="type['.$mk.']" id="type__2'.$_POST['rid'].$mk.'" required="" onchange="changetype('.$_POST['rid'].$mk.')"><option value="T" '.$texttype.'>Text</option><option value="S" '.$selecttype.'>Select</option></select></td><td class="p-2  text-center"><div class="checkbox-fade fade-in-primary m-0"><label class="form-label"><input aria-label="Field Employee[" type="checkbox" value="Y" name="field_employee['.$mk.']" '.$emp_check_teb.'><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></td><td class="p-2  text-center"><div class="checkbox-fade fade-in-primary m-0"><label class="form-label"><input aria-label="Field Ro[" type="checkbox" value="Y" name="field_ro['.$mk.']" '.$RO_check_teb.'><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></td><td class="p-2  text-center"><div class="checkbox-fade fade-in-primary m-0"><label class="form-label"><input aria-label="Field Hod[" type="checkbox" value="Y" name="field_hod['.$mk.']" '.$HOD_check_teb.'><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></td><td onclick="'.$deletfun.'" role="button" tabindex="0" onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()"><button type="button" class="btn p-0 border-0 bg-transparent fa fa-trash text-danger" tabindex="0" onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()"></button></td></tr>';
        $mk++;
}
        $html_page .= '</tbody></table><button type="button" class="btn btn-sm font-weight-bolder btn-light-primary btn-primary" onclick="subHeaderTableAdded('.$_POST['rid'].')"><i class="fa fa-plus"></i>Add</button></div>';

        $html_page .= '<div class="form-group dynamicpageshow" '.$tableshow.'><span class="form-label">Sub Header Addmore</span></br><input aria-label="Sub Header Addmore" class="js-multidynamic'.$_POST['rid'].'" type="checkbox" id="sub_header_addmore_check" '.$dynamic_page.' value="0" name="sub_header_addmore" '.$sub_header_addmore.'  onchange="addMoreButtonSetting('.$_POST['rid'].')"/></div><div class="form-group dynamicpageshow" '.$tableshow.'><span class="form-label">Sub Header Default row</span><input aria-label="Sub Header Default Row" type="number" class="form-control " name="sub_header_default_row" value="'.$paheaders['sub_header_default_row'].'"/></div>';
        $html_page .= '<div class="form-group"><span class="form-label">Header Display Setting<span class="text-danger">*</span></span><div class=""><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="Displayemployee" type="checkbox" value="Y" name="displayemployee" '.$emp_check.'><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span>Employee</span></label></div><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="Displayro" type="checkbox" value="Y" name="displayro" '.$RO_check.'><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span>RO</span></label></div><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="Displayhod" type="checkbox" value="Y" name="displayhod" '.$HOD_check.'><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span>HOD</span></label></div></div></div><button type="button" onclick="performanaceAppraiserHeaderSbt()" class="btn btn-primary" >Update</button>';
                            $paheadersrow = $paheaders['id'];

                            $view_html_page = '<a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2  bg-primary" >
                            <div class="row">
                            <div class="col-lg-11">
                            <span id="header_name_'.$paheaders['id'].'">'.$paheaders['header_name'].'</span>
                            <p class="f-w-400" id="header_comment_'.$paheaders['id'].'">'.$paheaders['header_comment'].'</p>
                            </div>
                            <div class="col-lg-1">
                            <button type="button" class="btn p-0 border-0 bg-transparent fa fa-edit" onclick="editHeader('.$paheaders['id'].')" tabindex="0" onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()"></button>
                            </div>
                            </div>
                            </a>';
                    $view_html_page .= '<div class="accordion-desc"><p class="dynamic_page_Y_'.$paheaders['id'].'" style="display:none">Page URL</p> <div class="table-responsive dynamic_page_N_'.$paheaders['id'].'" ><table class="table table-bordered" id="dinamictable_'.$paheaders['id'].'"><thead><tr>';
                                $fildcnt = 1; foreach($sub_header_name as $sub_headerres){  $idd = "deletrow_".$paheaders['id'].$fildcnt;
                                $view_html_page .= '<th scope="col" class="head_text_'.$paheaders['id'].$fildcnt.' '.$idd.'">'.$sub_headerres['field_name'].'</th>';
                                $fildcnt++;
                            }

                                $view_html_page .= '<th scope="col" class="addemorebutton_'.$paheaders['id'].'" >Action</th></tr></thead>';
                                $view_html_page .= '<tbody >';
                                $hedcnt[$paheadersrow] = 1;
                                for($hed = 0;$hed < $paheaders['sub_header_default_row'];$hed++){

                                    $view_html_page .= '<tr id="deleterow_'.$paheaders['id'].$hedcnt[$paheadersrow].'">';
                                    $fieldcnt = 1; foreach($sub_header_name as $sub_headerres){
                                        $idd = "deletrow_".$paheaders['id'].$fieldcnt;
                                        $texttype = $sub_headerres["type"] =="T"?"selected":"";
                                        $selecttype = $sub_headerres["type"] =="S"?"selected":"";

                                        $view_html_page .= '<td class="'.$idd.'"><div  id="chengetypetextselect_'.$paheaders['id'].$fieldcnt.'">';
                                        if($sub_headerres["type"] =="S"){
                                            $view_html_page .= '<select aria-label="Select option"><option>Yes</option><option>No</option><option>maybe</option></selecte>';
                                        }else{
                                            $view_html_page .= '<input aria-label="Input field" type="text" class="form-control" id="" value="">';
                                        }
                                        $view_html_page .= '</div></td>';
                                        $fieldcnt++;
}

                                        $view_html_page .= '<td class="addemorebutton_'.$paheaders['id'].'"><button type="button" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger btn-primary" ><i class="fa fa-trash"></i>Delete</button></td></tr>';
                                        $hedcnt[$paheadersrow]++;
}
                                        $view_html_page .= '</tbody></table><button type="button" class="btn btn-sm font-weight-bolder btn-light-primary btn-primary addemorebutton_'.$paheaders['id'].'" ><i class="fa fa-plus"></i>Add</button>';
                                        $view_html_page .= '</div></div>';


                                        echo $returnarray = json_encode(array("LASTID"=>$_POST['rid'],'INSERT_PAGE'=>$html_page,"VIEW_HTML_PAGE"=>$view_html_page,"EXICUTION_STATUS"=>$response1));
                                    }else{ echo $returnarray = json_encode(array("EXICUTION_STATUS"=>$response1));
}
