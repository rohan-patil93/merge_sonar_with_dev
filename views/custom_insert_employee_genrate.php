<?php
           $cat_id =2;
        $option_c = $_GET['option'];
        $RID_c = $_GET['Rid'];
        if(isset($_POST['submit'])){
            $_POST['category_id'] = $cat_id;
            $url = API."?mode=genratecandidatetoemployee&method=post";
            $response = file_post_contents($url, $_POST, 'insert');
        if ($response) {  header("location:index.php?option=".$option_c."&Rid=".$RID_c);
}
        }
        $vdata1["where"] = array('is_active'=> 0,'category_id'=>$cat_id);
        $vdata = urlencode(json_encode($vdata1));
        $vurl = API."?mode=gettabledetailswithconditions&table=document_lists&data=".$vdata;
        $vdata_details = callAPI($vurl);
        $vdata_details = json_decode($vdata_details,1);

?>
<script type="text/javascript">
    function validateForm() {
        var x = document.getElementById('email').value;
        if (x == ""){
            document.getElementById('email_err').style.display = "block";
            return false;
        }
    }
</script>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="d-flex flex-row">
            <?php  include_once 'candidate_side_nav.php'; ?>
        <?php
        $whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'candidate_id'=>$candidate_id,'is_active'=>0,'category_id'=>$cat_id);
          $wdata = urlencode(json_encode($whrcon));
          $url = API."?mode=gettabledetailswithconditions&table=candidate_documents&data=".$wdata;
          $response = callAPI($url);
          $fdata = json_decode($response,1);
          foreach ($fdata as $value) {
              $checkl[] = $value['document_list_id'];
          }
         ?>
        <div class="flex-row-fluid ml-lg-8">
            <div class="row">
                <div class="col-lg-12">
                <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">

                    <div class="card card-custom " id="kt_page_sticky_card">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">&nbsp;<?php echo "Genrate Employee";  ?> </h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="example-email-input" class="col-2 col-form-label">Email</label>
                                <div class="col-10">
                                 <input aria-label="Email" class="form-control" type="email"  value="" name="email" id="email"/>
                                 <div id="email_err" class="text-danger" style="display: none;"> Please Enter Email</div>
                                </div>

                            </div>
                            <div class="form-group row">
                                <label for="example-email-input" class="col-2 col-form-label">Phone</label>
                                <div class="col-10">
                                 <input aria-label="Phone" class="form-control" name="phone" value="<?php echo $data['phone'];  ?>" />
                                </div>
                             </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-4">
                                <input type="hidden" name="candidate_id" value="<?php echo $candidate_id;  ?>" />
                                <input type="hidden" name="submit" value="submit" />
                                 <button id="kt_login_signin_submit" class="btn btn-primary mr-2"   name="submitto" value="exit"><i class="la la-check"></i>Genrate Employee</button>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
