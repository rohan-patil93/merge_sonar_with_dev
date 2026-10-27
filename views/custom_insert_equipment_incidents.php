<?php
if(isset($_POST['submit_equipment'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $image_1 = imageupload('all','image_1',$_POST['hid_image_1']);
    $data['image_1'] = $image_1;
    $image_2 = imageupload('all','image_2',$_POST['hid_image_2']);
    $data['image_2'] = $image_2;

    $url = API."?mode=insertequipmentincidents&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    echo "<pre>"; print_r($res); echo "</pre>";
    $response = $res['res'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
        <?php
    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}

$_SESSION['apply_js']['multiselest'] = "1";
$rid1 =  encodeId($_GET['Rid']);
$rid = urldecode($rid1);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url = API."?mode=getequipmentincidents&rid=".$rid;
$response = callAPI($url);
$result = json_decode($response,1);
$equipments = $result['equipments'];
?>
<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
<div class="card">
    <div class="card-header">
        <h5>Equipment Incidents</h5>
        <div class="card-header-right">
            <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>
        </div>
    </div>
    <div class="card-body m-l-50  m-r-50">
        <form method="post" id="insertbuildings" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-1">
                </div>
                <div class="col-lg-2">
                    <span class="form-label">Scan QR Code</span>
                    <div class="col-lg-9 col-xl-6">
                        <div class="profile-pic">
                            <label class="-label" for="qr_image">
                                <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                            </label>
                            <input type="file" name="qr_image" id="qr_image" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                            <img src="<?php echo "uploads/all/".$buildings['qr_image'] ; ?>" id="outputdisplay_logo" width="200" alt="Logo" />
                            <input type="hidden" name="hid_image" value="<?php echo $buildings['qr_image'] ;  ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group col-lg-1">
                    <span  class="form-label">OR </span>
                </div>
                <div class="form-group col-lg-8">
                    <div class="row">
                        <div class="col-lg-6">
                            <span class="form-label d-block">Equipment</span>
                            <select aria-label="Equipment" class="js-example-basic-single" name="equipment_id">
                                <option value="">Select Any One</option>
                                <?php foreach($equipments as $stytpe){ ?>
                                <option value="<?php echo $stytpe['id']; ?>" <?php echo $stytpe['id']==$_POST['equipment_id']?"selected":""; ?>><?php echo $stytpe['equipment_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <span class="form-label d-block">Note</span>
                            <textarea aria-label="Note" class="form-control" name="note" ></textarea>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="form-group col-lg-6">
                            <span class="form-label">Equipment Image 1</span>
                            <div class="col-lg-9 col-xl-6">
                                <div class="profile-pic">
                                    <label class="-label" for="equipment_image_1">
                                        <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                    </label>
                                    <input type="file" name="image_1" id="equipment_image_1" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                                    <img src="<?php echo "uploads/all/".$buildings['image_1'] ; ?>" id="outputequipment_image_1" width="200" alt="Attachment Preview" />
                                    <input type="hidden" name="hid_image_1" value="<?php echo $buildings['image_1'] ;  ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span class="form-label">Equipment Image 2</span>
                            <div class="col-lg-9 col-xl-6">
                                <div class="profile-pic">
                                    <label class="-label" for="equipment_image_2">
                                        <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                    </label>
                                    <input type="file" name="image_2" id="equipment_image_2" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                                    <img src="<?php echo "uploads/all/".$buildings['image_2'] ; ?>" id="outputequipment_image_2" width="200" alt="Attachment Preview" />
                                    <input type="hidden" name="hid_image_2" value="<?php echo $buildings['image_2'] ;  ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer pt-0 mt-3">
                <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                <button type="button" class="btn btn-primary" name="submit_equipment" onclick="submitBuildingFloorPlans()"> <?php echo $rid !=""?"Update":"Submit";?></button>
            </div>
        </form>
    </div>
</div>
