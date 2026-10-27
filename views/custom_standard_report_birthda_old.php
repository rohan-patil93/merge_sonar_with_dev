<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
$postdata = array();
if(isset($_POST['applyform'])) {
  $postdata = $_POST;
}
$url = API."?mode=togetemployeeageprofile&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);
$menus = $result['menu_data'];
$cdaa = array();
foreach($result['age_range'] as $agerr){
  $cdaa[] = $agerr['age_range'].":".$agerr['employee_count'];
}
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr']=1;
?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Birthdays</h5>
      <h6 class="mt-4"><?php echo count((array)$result['emp_data']); ?> People</h6>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Age Profile" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Age Profile" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <form method="post" action="">
      <div class="row col-lg-12 ">
        <div class="col-lg-4">
          <nav class="all-employee">
            <ul>
              <li class="d-block border dropbtn"><a href="#">All Employees</a>
                <ul>
                  <?php foreach($menus as $mkey=>$menu){ ?>
                  <li><a href="#"><?php echo $menu['name']; ?> <i class="ti-angle-right float-end f-12 m-t-5"></i></a>
                    <ul>
                      <?php foreach ($menu['data'] as  $fkey=>$menuvalue) { ?>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input aria-label="Input field" <?php if(in_array($fkey,$_POST[$mkey])){ echo "checked";
} ?> class="border-checkbox" name="<?php echo $mkey; ?>[]" value="<?php echo $fkey;?>" type="checkbox" id="<?php echo $mkey."_".$fkey; ?>">
                            <label class="form-label border-checkbox-label" for="<?php echo $mkey."_".$fkey; ?>"><?php echo $menuvalue;?></label>
                          </div>
                        </div>
                      </li>
                      <?php } ?>
                    </ul>
                  </li>
                  <?php } ?>
                </ul>
              </li>
            </ul>
          </nav>
        </div>
        <div class="col-lg-2 mt-1">
            <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                Apply
              </button>
            <input type="hidden" name="submit" value="applyform">
          </div>
        </form>

      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="col-lg-12 mt-4">
          <h3 class="f-w-600 border-bottom pb-2">April</h3>
        </div>
        <div class="col-lg-12">
          <span class="text-end m-r-10 m-l-10 f-w-600 f-20">8</span>
          <span class="">Jake Huang(27)</span>
        </div>
        <div class="col-lg-12">
          <span class="text-end m-r-10 m-l-10 f-w-600 f-20">12</span>
          <span class="">Samantha Simmons(30) ,James Harris(30)</span>
        </div>
        <div class="col-lg-12">
          <span class="text-end m-r-10 m-l-10 f-w-600 f-20">19</span>
          <span class="">Jeff Tobler(32)</span>
        </div>
        <div class="col-lg-12">
          <span class="text-end m-r-10 m-l-10 f-w-600 f-20">23</span>
          <span class="">Omar Waldwin(28)</span>
        </div>
        <div class="col-lg-12">
          <span class="text-end m-r-10 m-l-10 f-w-600 f-20">29</span>
          <span class="">Zack Miller(24)</span>
        </div>
        <div class="col-lg-12 mt-4">
          <h3 class="f-w-600 border-bottom pb-2">May</h3>
        </div>
        <div class="col-lg-12">
          <span class="text-end m-r-10 m-l-10 f-w-600 f-20">8</span>
          <span class="">Jake Huang(27)</span>
        </div>
        <div class="col-lg-12">
          <span class="text-end m-r-10 m-l-10 f-w-600 f-20">12</span>
          <span class="">Samantha Simmons(30) ,James Harris(30)</span>
        </div>
        <div class="col-lg-12">
          <span class="text-end m-r-10 m-l-10 f-w-600 f-20">19</span>
          <span class="">Jeff Tobler(32)</span>
        </div>
    </div>
  </div>
</div>
