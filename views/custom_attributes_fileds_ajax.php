<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
 $page = $_POST['page']; 
 //print_r($_POST);
if($page == "elementordesign"){
	$filedTypes ="";
	foreach($filed_array as $key => $value){
		$filedTypes .='<option value="'.$key.'" >'.$value.'</option>';
	} 
	$message ='<div class="card card_count deleterow'.$_POST['id'].'">
	<div class="card-header p-2 f-w-600" id="headingOne'.$_POST['id'].'">
	<div class="card-title collapsed textboxclass'.$_POST['id'].'" data-bs-toggle="collapse" data-bs-target="#collapseOnee'.$_POST['id'].'">
	<i class="flaticon2-cancel-music" onclick="deleterowinputtype('.$_POST['id'].')"></i> Name
	</div>
	</div>
	<div id="collapseOnee'.$_POST['id'].'" class="collapse" data-parent="#accordionExample5">
	<div class="card-body">
	<div class="example-preview">
	<ul class="nav nav-pills nav-fill">
	<li class="nav-item">
	<a class="nav-link active" id="content_'.$_POST['id'].'" data-bs-toggle="tab" href="#content-'.$_POST['id'].'"   >
	<span class="nav-text">Content</span>
	</a>
	</li>
	<li class="nav-item">
	<a class="nav-link" id="advanced_'.$_POST['id'].'" data-bs-toggle="tab" href="#advanced-'.$_POST['id'].'"  aria-controls="profile" >
	<span class="nav-text">Advanced</span>
	</a>
	</li>
	</ul>
	<div class="tab-content mt-5" id="myTabContent4">
	<div class="tab-pane fade show active" id="content-'.$_POST['id'].'" role="tabpanel" aria-labelledby="content_1">
	<div class="form-group row">
	<label class="col-6 col-form-label">Field Name</label>
	<div class="col-6">
	<input class="form-control elementor_fieldname space elementor_fieldname'.$_POST['id'].'" data-id="'.$_POST['id'].'"  type="text" name="fieldname[]" value="" />
	</div>
	</div>

	<div class="form-group row">
	<label  class="col-6 col-form-label ">Field Type</label>
	<div class="col-6">
	<select class="form-control elementor_fieldtype elementor_fieldtype'.$_POST['id'].' form-select js-example-basic-single" name="elementorfieldtype[]" data-elementory_id="inputbox'.$_POST['id'].'" data-id="'.$_POST['id'].'"  >
	<option value="varchar" >String</option>
	<option value="text" >Description</option>
	<option value="int" >Number</option>
	<option value="decimal">Decimal</option>
	<option value="date">Date</option>
	<option value="datetime" >Date Time</option>
	</select>
	</div>
	</div>
	<div class="form-group row">
	<label class="col-6 col-form-label">Position</label>
	<div class="col-6">
	<input class="form-control" type="text" name="display_postion[]" value="'.$_POST['count'].'" />
	</div>
	</div>
	<div class="form-group row">
	<label class="col-6 col-form-label">Label & Input One Line</label>
	<div class="col-6">
	<span class="switch switch-icon">
	<label>
	<input type="checkbox" data-id="'.$_POST['id'].'" class="elementory_labelandinputtype js-switch_append'.$_POST['increment'].'" data-elementory_id="inputbox'.$_POST['id'].'" data-label="inputlabel'.$_POST['id'].'" data-div="divbox'.$_POST['id'].'"  
	name="labelandinputineline['.$_POST['increment'].']" />
	<span></span>
	</label>
	</span>
	</div>
	</div>

	<div class="form-group row">
	<label  class="col-6 col-form-label ">Type</label>
	<div class="col-6">
	<select class=" elementor_text_box_type form-select js-example-basic-single" onchange="Getinserttype(this.id,this.value)" id="type" name="elementorytexttype[]" data-elementory_id="inputbox'.$_POST['id'].'" data-id="'.$_POST['id'].'" >'.$filedTypes.'
	</select>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-6 col-form-label">Label</label>
	<div class="col-6">
	<input class="form-control elementory_lable elementory_lable'.$_POST['id'].'" data-labletext="labletext'.$_POST['id'].'" data-elementory_id="inputlabel'.$_POST['id'].'" data-id="'.$_POST['id'].'"  type="text" value="label"  name="elementorylabel[]" />
	</div>
	</div>
	<div class="form-group row">
	<label class="col-6 col-form-label">Display</label>
	<div class="col-6">
	<span class="switch switch-icon">
	<label>
	<input type="checkbox" class="display_status js-switch_append'.$_POST['increment'].'" data-id="'.$_POST['id'].'" checked name="display['.$_POST['increment'].']" />
	<span></span>
	</label>
	</span>
	</div>
	</div>
	<div class="form-group row">
	<label class="col-6 col-form-label">Insert Display List</label>
	<div class="col-6">
	<span class="switch switch-icon">
	<label>
	<input type="checkbox" checked  class="display_status js-switch_append'.$_POST['increment'].'" data-id="'.$_POST['id'].'" name="insert_display_list['.$_POST['increment'].']" />
	<span></span>
	</label>
	</span>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-6 col-form-label">Accordion</label>
	<div class="col-6">
	<select class=" js-example-basic-single" name="accordion[]" >
	<option value="N" >Normal</option>
	<option value="R" >Rigth</option>
	<option value="L" >Left</option>
	</select>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-6 col-form-label">Wizard</label>
	<div class="col-6">
	<select class=" js-example-basic-single" id="exampleSelect1" name="insert_view_wizard[]">
	<option value="1" selected>1- Wizard </option>
	<option value="2" >2- Wizard </option>
	<option value="3" >3- Wizard </option>
	<option value="4">4- Wizard </option>
	<option value="5">5- Wizard </option>
	<option value="6" >6- Wizard </option>
	<option value="7" >7- Wizard </option>
	<option value="8" >8- Wizard </option>
	<option value="9" >9- Wizard </option>
	<option value="10" >10- Wizard </option>
	</select>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-6 col-form-label">Placeholder</label>
	<div class="col-6">
	<input class="form-control elementory_placeholder  elementory_placeholder'.$_POST['id'].'" type="text" value="" name="elementorplacehilder[]" data-elementory_id="inputbox'.$_POST['id'].'" id=""/>
	</div>
	</div>
	<div class="form-group row">
	<label class="col-6 col-form-label">Required</label>
	<div class="col-6">
	<span class="switch switch-icon">
	<label>
	<input type="checkbox" data-requerd= "labletextrequerd'.$_POST['id'].'" data-label="inputlabel'.$_POST['id'].'" data-div="divbox'.$_POST['id'].'" class="elementory_required js-switch_append'.$_POST['increment'].'" data-elementory_id="inputbox'.$_POST['id'].'" name="elementoryrequerd['.$_POST['increment'].']"/>
	<span></span>
	</label>
	</span>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-6 col-form-label">Column Width</label>
	<div class="col-6">
	<select class="form-control elementory_width form-select js-example-basic-single" data-id="'.$_POST['id'].'" data-elementory_id="divbox'.$_POST['id'].'" name="elementorycolunmwidth[]"  data-select2-id="" tabindex="-1" aria-hidden="true">'.optiontype(6,$widtharray).'</select>
	</div>
	</div>
	</div>
	<div class="tab-pane fade" id="advanced-'.$_POST['id'].'" role="tabpanel" aria-labelledby="advanced_1">
	<div class="form-group row">
	<label  class="col-5 col-form-label">Default Value</label>
	<div class="col-7">
	<div class="input-group">
	<input type="text" class="form-control valuesdefoult" data-id="'.$_POST['id'].'" name="valuesdefoult[]" value="" >
	<div class="input-group-append">
	<button class="btn btn-secondary" type="button">
	<i class="flaticon-coins"></i></button>
	</div>
	</div>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">ID Name</label>
	<div class="col-7">
	<input class="form-control elementory_id" name="elementoryid[]" type="text" data-elementory_id="inputbox'.$_POST['id'].'" value="id name" id=""/>
	</div>
	</div>
	<div class="form-group row">
	<div class="col-12">
	<p class="text-muted font-size-xs">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s</p>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">Shortcode</label>
	<div class="col-7">
	<input class="form-control" type="text" value="" id=""/>
	</div>
	</div>
	</div>
	</div>
	</div></div></div></div>';
	$inputtype ='<div class="divbox'.$_POST['id'].'  col-lg-6" id="divid'.$_POST['id'].'" >
	<label class="textcapital col-form-label text-lg-right inputlabel'.$_POST['id'].'"><span class="labletext'.$_POST['id'].'">labe</span><span class="text-danger labletextrequerd'.$_POST['id'].'" style="display:none"  > * </span></label>	
	<div class="inputtypediv'.$_POST['id'].'" >			
	<input type="text"  class="form-control form-control-lg inputbox'.$_POST['id'].'" name="inputtype'.$_POST['id'].'" placeholder="" />
	</div></div>';
	echo json_encode(array('setting'=>$message,'textfileds'=>$inputtype));
}

if($page== "elementordesignbutton"){
	$message = '<div class="card buttons_count mt-3 deletebutton'.$_POST['id'].'">
	<div class="card-header p-2 f-w-600" id="headingOnebutton'.$_POST['id'].'">
	<div class="card-title collapsed title'.$_POST['id'].'" data-bs-toggle="collapse" data-bs-target="#collapseOneebutton'.$_POST['id'].'" aria-expanded="false">
	<i class="flaticon2-cancel-music" onclick="deletebutton('.$_POST['id'].')"></i> Save
	</div>
	</div>
	<div id="collapseOneebutton'.$_POST['id'].'" class="collapse" data-parent="#accordionExample5" style="">
	<div class="card-body">
	<div class="form-group row">
	<label  class="col-5 col-form-label">Function Type</label>
	<div class="col-7">
	<select class="form-control function_type function_type'.$_POST['id'].' form-select js-example-basic-single" data-id="'.$_POST['id'].'"  name="functiontype[]" >
	<option value="saveandcontinue" >Save & continue</option>
	<option value="save" >Save</option>
	<option value="update"  >Update</option>
	<option value="print" >Print</option>
	</select>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">Size</label>
	<div class="col-7">
	<select class="form-control button_size button_sizes'.$_POST['id'].' form-select js-example-basic-single" id="" data-id="'.$_POST['id'].'" name="buttonsize[]" >
	<option value="btn-xs" >Extra Small</option>
	<option value="btn-sm" >Small</option>
	<option value=""  selected>Medium</option>
	<option value="btn-lg" >Large</option>
	<option value="btn-lg btn-block" >Extra Large</option>
	</select>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">Column Width</label>
	<div class="col-7">
	<select class="form-control buttonwidth buttonwidth'.$_POST['id'].' form-select js-example-basic-single" id="" data-id="'.$_POST['id'].'" name="buttonwidth[]" data-select2-id="" tabindex="-1" aria-hidden="true">'.optiontype(6,$widtharray).'</select>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">Button Position</label>

	<input type="hidden"  id="buttonposition'.$_POST['id'].'" name="buttonposition[]" value="text-left">

	<div class="col-7">
	<button type="button"  value="text-left" data-id="'.$_POST['id'].'" class="btn btn-outline-secondary">
	<i class="fa fa-align-left"></i>
	</button>
		<button type="button" value="text-center" data-id="'.$_POST['id'].'" class="btn btn-outline-secondary">
	<i class="fa fa-align-justify"></i>
	</button>
	<button type="button" value="text-right" data-id="'.$_POST['id'].'" class="btn btn-outline-secondary">
	<i class="fa fa-align-right"></i>
	</button>

	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">Button Name</label>
	<div class="col-7">
	<input class="form-control buttonname buttonname'.$_POST['id'].'" name="buttonname[]" data-id="'.$_POST['id'].'" type="text" value="save" id=""/>
	</div>
	</div>
	<div class="form-group row">
	<label  class="col-5 col-form-label">Form Class</label>
	<div class="col-7">
	<input class="form-control button_class button_class'.$_POST['id'].'" name="buttonclass[]" data-id="'.$_POST['id'].'" type="text"  value="save" id=""/>
	</div></div></div></div></div>';

	$button = '<div class="col-lg-6" id="buttondiv'.$_POST['id'].'">

	<button type="button" class="btn btn-primary mr-2 textcapital" id="buttonid'.$_POST['id'].'"><i class="la la-check"></i>Save</button>
	</div>';		
	echo json_encode(array('setting'=>$message,'textfileds'=>$button));

}
if($page == "fileuploadingstatusdisplay"){
	$message .= '<div class="col-lg-6 fileuploadui" id="fileuploaduiid">
	<label class="fileuplodinglablelui">File Upload</label>
	<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="dropzone dropzone-default dropzone-primary digits" id="kt_dropzone_2">
	<div class="dropzone-msg dz-message needsclick">
	<h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
	<span class="dropzone-msg-desc">Upload up to 10 files</span>
	</div></div></div></div>
	<div class="col-lg-6 fileuploaddeleteui" id="fileuploaddeleteuiid">
	<label class="">Delete list</label>
	<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="dropzone dropzone-default dropzone-primary digits" id="kt_dropzone_2">
	</div></div></div>';
	echo $message;			
}
if ($page == 'elementortype'){
	textboxes($_POST['id'],$_POST['type'],""); 
	exit;
}
?>