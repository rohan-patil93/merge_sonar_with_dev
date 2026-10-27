<?php
$_SESSION['apply_js']['multiselest'] = "1";
echo "<pre>"; print_r($_POST['password_policy']); echo "</pre>";
$config = $_SESSION['config'];
if(isset($_POST['submit_policy'])){



    $post['password_policy'] = $password_policy;
    $post['updated_by'] = $_SESSION['userid'];

    $url = API."?mode=updaterowbasepimarydata&table=config&rid=1&method=post";
}
?>
<div class="card config-card">
  <div class="card-header">
    <h5>Password Policies</h5>
    <div class="card-header-right">
      <a href="" class="btn waves-effect waves-light mb-2 desktop-view">
        <i class="icofont icofont-reply"></i>
        Back
      </a>
    </div>
  </div>
  <form method="post">
    <div class="row">
      <div class="col-lg-8 mx-auto p-3">

        <div class="card-body p-0">
          <div class="mt-5 info p-2 b-radius-5 border-bottom-0">
            <span class="h6 f-w-600">Strengthen Password</span>
          </div>

          <form>
            <div class="row row-align mb-3">
              <div class="col-lg-5 text-sm-end">
                <span class="form-label">Require strengthened password rules</span>
              </div>
              <div class="col-lg-7">
                <div class="form-check form-switch ps-0">
                  <input aria-label="Password Policy" class="form-check-input form-check-input-custom ms-0" type="checkbox" role="switch" aria-checked="<?php echo $config['password_policy_applicable'] == 'Y' ? 'true' : 'false'; ?>" name="password_Policy" value="Y" <?php echo $config['password_policy_applicable'] =="Y"?"checked":""; ?> >
                </div>
              </div>
            </div>

            <div class="mt-5 mb-3 info p-2 b-radius-5 border-bottom-0 ">
              <span class="h6 f-w-600">Password Rules</span>
            </div>

            <div class="row row-align mb-3">
              <div class="col-lg-5 text-sm-end">
                <label for="minLength" class="form-label">Minimum password length</label>
              </div>
              <div class="col-lg-3">
                <input type="number" class="form-control form-control-sm" id="minLength" name="password_policy[password_length]" value="" min="1" max="32">
                <div class="form-hint">From 1 to 32</div>
              </div>
            </div>

            <div class="row row-align mb-3">
              <div class="col-lg-5 text-sm-end">
                <span class="form-label">Require numeric character</span>
              </div>
              <div class="col-lg-7">
                <div class="form-check form-switch ps-0">
                  <input aria-label="Password Policy[numeric Character]" class="form-check-input form-check-input-custom ms-0" type="checkbox" name="password_policy[numeric_character]" value="Y" role="switch" aria-checked="true" checked>
                </div>
              </div>
            </div>

            <div class="row row-align mb-3">
              <div class="col-lg-5 text-sm-end">
                <span class="form-label">Require special character</span>
              </div>
              <div class="col-lg-7">
                <div class="form-check form-switch ps-0">
                  <input aria-label="Password Policy[special Character]" class="form-check-input form-check-input-custom ms-0" name="password_policy[special_character]" type="checkbox" value="Y" role="switch" aria-checked="false">
                </div>
              </div>
            </div>

            <div class="row row-align mb-3">
              <div class="col-lg-5 text-sm-end">
                <span class="form-label">Require upper & lower case</span>
              </div>
              <div class="col-lg-7">
                <div class="form-check form-switch ps-0">
                  <input aria-label="Password Policy[require Upper Lower Case]" class="form-check-input form-check-input-custom ms-0" name="password_policy[require_upper_lower_case]" type="checkbox" value="Y" role="switch" aria-checked="true" checked>
                </div>
              </div>
            </div>

            <div class="row row-align mb-3">
              <div class="col-lg-5 text-sm-end">
                <span class="form-label">Reject passwords that contain user's firstname, surname or username</span>
              </div>
              <div class="col-lg-7">
                <div class="form-check form-switch ps-0">
                  <input aria-label="Password Policy[reject Password Contain User Detail]" class="form-check-input form-check-input-custom ms-0" name="password_policy[reject_password_contain_user_detail]" type="checkbox" value="Y" role="switch" aria-checked="true" checked>
                </div>
              </div>
            </div>

            <div class="row row-align mb-3">
              <div class="col-lg-5 text-sm-end">
                <span class="form-label">New password must be different to the last 3 passwords</span>
              </div>
              <div class="col-lg-7">
                <div class="form-check form-switch ps-0">
                  <input aria-label="Password Policy[password Must Be Different Last Password]" class="form-check-input form-check-input-custom ms-0" name="password_policy[password_must_be_different_last_password]" type="checkbox" value="Y" role="switch" aria-checked="true" checked>
                </div>
              </div>
            </div>

            <div class="row row-align mb-3">
              <div class="col-lg-5 text-sm-end">
                <span class="form-label">Cannot change password more than once in a day</span>
              </div>
              <div class="col-lg-7">
                <div class="form-check form-switch ps-0">
                  <input aria-label="Password Policy[cannot Change Password More Than Once In A Day]" class="form-check-input form-check-input-custom ms-0" name="password_policy[cannot_change_password_more_than_once_in_a_day]" type="checkbox"  value="Y" role="switch" aria-checked="false">
                </div>
              </div>
            </div>
            <div class="row row-align mb-3">
              <div class="col-lg-5 text-sm-end">
                <label for="expiryDays" class="form-label">Password expiry days</label>
              </div>
              <div class="col-lg-3">
                <input type="number" class="form-control form-control-sm" id="expiryDays" name="password_policy[expiry_days]" value="365">
                <div class="form-hint">Empty this for no expiry. Minimum length is 30 days</div>
              </div>
            </div>



            <div class="row">
              <div class="col-lg-12">
                <button type="submit" name="submit_policy" class="btn btn-primary px-4 shadow-sm" style="font-size: 14px; border-radius: 4px;">Submit</button>
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
  </form>
</div>

