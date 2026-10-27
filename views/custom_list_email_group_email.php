<?php
$rid =  encodeId($_GET['RID']);
$emailurl = API."?mode=getgroupemaildetails&group_id=$rid";
$emailgroupdata = callAPI($emailurl);
$emailgroupdata = json_decode($emailgroupdata,1);
?>


                    <div class="d-flex flex-column-fluid">
                        <div class="container-fluid">
                            <div class="card card-custom ">
      <div class="card-header table-card-header mb-2">
       <h5>Email Group</h5>
       <div class="card-header-right table-card-header ">
        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
          <i class="icofont icofont-reply"></i>
          Back
        </a>
      </div>
    </div>

                <div class="card-body">
                    <div class="row j-pro">
                                <?php foreach ($emailgroupdata as $data){ ?>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="card card-custom gutter-b card-stretch">
                                <div class="card-body  pt-2">
                                    <div class="card-header">
                                       <h5><?php echo ucwords($data['name']); ?></h5>
                                       <div class="card-header-right table-card-header ">
                                            <button type="button" class="btn btn-primary btn-sm text-uppercase font-weight-bolder mt-5 mt-sm-0 mr-auto mr-sm-0 ml-sm-auto">Send</button>

                                      </div>
                                    </div>
                                    <div class="align-middle mt-4">
                                        <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($data['name']); ?></span>
                                        <div class="d-inline-block width73">
                                            <a href="#!" class="text-danger text-capitalize"><span class="f-12"><?php echo $data['email']; ?></span></a>

                                        </div>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <button type="button" class="btn btn-primary font-weight-bolder btn-sm text-uppercase">view project</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
