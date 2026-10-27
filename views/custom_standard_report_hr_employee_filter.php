<div class="col-lg-<?php echo $dynamiccollg!=''?$dynamiccollg:'4'?>">
  <nav class="all-employee">
    <ul>
      <li class="d-block border dropbtn"><a href="#">All Employees</a>
        <ul>
          <?php foreach($menus as $mkey=>$menu){ ?>
            <li><a href="#"><?php echo $menu['name']; ?> <i class="ti-angle-right float-end f-12 m-t-5"></i></a>
              <ul>
                <?php if(!empty($menu['data'])){
                    foreach ((array)$menu['data'] as  $fkey=>$menuvalue) { ?>
                  <li>
                    <div class="border-checkbox-section">
                      <div class="border-checkbox-group border-checkbox-group-primary">
                        <input aria-label="Input field" <?php if(in_array($fkey,(array)$_POST[$mkey])){ echo "checked";
} ?> class="border-checkbox" name="<?php echo $mkey; ?>[]" value="<?php echo $fkey;?>" type="checkbox" id="<?php echo $mkey."_".$fkey; ?>">
                        <label class="form-label border-checkbox-label" for="<?php echo $mkey."_".$fkey; ?>"><?php echo $menuvalue;?></label>
                      </div>
                    </div>
                  </li>
                <?php }
                }else{?>
                  <li>
                    <div class="border-checkbox-section text-danger f-w-600">
                      <?php echo $menu['name']." is not defined "; ?>
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
