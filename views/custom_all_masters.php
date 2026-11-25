<?php
$emailurl = API."?mode=togetmastertablesdetais";
//echo $emailurl; exit; togetmastertablesdetais
$emailgroupdata = callAPI($emailurl);
$datas = json_decode($emailgroupdata,1);
?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="alert alert-warning alert-dismissible">
            <strong><?= __("This function is used for maintaining") ?> <strong><?= __("Display Attributes") ?></strong>. <?= __("* represents mandatory fields.") ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header">
                        <h3 class="card-title"> <?php echo viewTitle($_GET['option']); ?> </h3>
                        <div class="card-header-right table-card-header ">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light btn-inverse btn-outline-inverse"><i class="icofont icofont-reply"></i> <?= __("Back") ?> </a>
                        </div>

        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
            

<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

   <!--Begin::Section-->
   <div class="row">
      <?php foreach ($datas as $data) {
         $page = toGetTableNameBaseApplicationPagesList('application_table_name', $data['application_table_name'],'normal');
         $display ['1'] = "Dynamic";
         $display ['2'] = "Custom";
         $discolor['1'] = "93a2dd";
         $discolor['2'] = "595d6e";
         ?>
         <div class="col-xl-3">

             <!--begin:: Portlet-->
             <div class="card card-custom gutter-b card-stretch">
                <div class="card-body" style="min-height:250px;">
                  <div style="position: absolute;right: 10px;">
                     <!--begin::Widget -->
                     <div class="kt-portlet__head-toolbar">
                        <a href="#" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-toggle="dropdown">
                           <i class="flaticon-more-1"></i>
                       </a>
                       <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right">
                           <ul class="kt-nav">
                              <li class="kt-nav__item">
                                 <a target="_blank" href="index.php?option=<?php echo $page['list_page_name']; ?>" class="kt-nav__link">
                                    <i class="kt-nav__link-icon flaticon-list-2"></i>
                                    <span class="kt-nav__link-text"><?= __("List") ?></span>
                                </a>
                            </li>
                            <li class="kt-nav__item">
                             <a target="_blank" href="index.php?option=<?php echo $page['insert_page_name']; ?>" class="kt-nav__link">
                                <i class="kt-nav__link-icon flaticon-add-circular-button"></i>
                                <span class="kt-nav__link-text"><?= __("Insert") ?></span>
                            </a>
                        </li>
                        <li class="kt-nav__item">
                         <a target="_blank" href="index.php?option=COM_INSERT_DISPLAY_ATTRIBUTES&table=<?php echo $data['application_table_name']; ?>"  class="kt-nav__link">
                            <i class="kt-nav__link-icon flaticon2-settings"></i>
                            <span class="kt-nav__link-text"><?= __("Settings") ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="kt-widget kt-widget--project-1">
      <div class="kt-widget__head">
         <div class="d-flex align-items-center">
            <div class="flex-shrink-0 mr-4 symbol symbol-60 symbol-circle">
               <span class="kt-media kt-media--lg kt-media--circle">
                  <div style="background: #fbce44;width: 50px;height: 50px; border-radius: 50%;text-align: center;">
                    <span style="color: #FFF;font-size: 25px;margin: 8px; font-weight: bold;"><?php echo initials(ucwords($data['application_table_name'])); ?></span>
                </div>
            </span>
        </div>
        <div class="d-flex flex-column m-l-10">
           <a href="#" class="kt-widget__title mb-2">
              <?php echo  ucwords(strtolower(str_replace("_", " ", $data['application_table_name']))); ?>
          </a>
          <span class="kt-widget__desc">
              <a href="#" class="btn btn-icon btn-circle" style="background-color: #<?php echo $discolor[$page['list_page_type']]; ?>;color:#fff;"  data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="<?= __("List") ?> <?php echo $display[$page['list_page_type']]; ?>"> L </a>
              <a href="#" class="btn btn-icon btn-circle" style="background-color: #<?php echo $discolor[$page['insert_page_type']]; ?>;color:#fff;" data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="<?= __("Insert") ?> <?php echo $display[$page['insert_page_type']]; ?>"><?php echo "I"; ?> </a>
              <a href="#" class="btn btn-icon btn-circle" style="background-color: #<?php echo $discolor[$page['edit_page_type']]; ?>;color:#fff;" data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="<?= __("Edit") ?> <?php echo $display[$page['edit_page_type']]; ?>"><?php echo "E"; ?>    </a>
              <a href="#" class="btn btn-icon btn-circle" style="background-color: #<?php echo $discolor[$page['view_page_type']]; ?>;color:#fff;" data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="<?= __("View") ?> <?php echo $display[$page['view_page_type']]; ?>">V    </a>
          </span>
      </div>
  </div>

</div>
<div class="kt-widget__body mt-2">
 <span class="kt-widget__text">
    <?php echo $data['help_text'] ?>
</span>
<div class="kt-widget__content mt-2">
    <?php ksort($data['status']); foreach ($data['status'] as  $value) {
        if($value['status_name'] != 'total' && $value['status_name'] != '' ){
           $count_value = $value['count']!='' ? $value['count'] : '0'; ?>

           <div class="kt-widget__details" style="margin-right: 2.7rem;">
               <span class="kt-widget__subtitle font-weight-bold mr-4">
                  <?= htmlspecialchars(__($value['status_name'] ?? '')) ?>
              </span>
              <div class="kt-widget__label">
                  <span class="btn btn-primary btn-sm font-weight-bold btn-upper btn-text"><?php echo $count_value  ; ?></span>
              </div>
          </div>
      <?php } elseif($value['status_name'] == 'total' ){ $total =$value['count'];
      }
      } ?>
  </div>
</div>
</div>
</div>

<div class="card-footer  align-items-center">
 <div class="kt-widget__wrapper">
    <div class="kt-widget__section float-left">
       <div class="kt-widget__blog">
          <i class="ti-list"></i>
          <a href="#" class="kt-widget__value kt-font-brand"><?php echo $total; ?> <?= __("Recards") ?></a>
      </div>
                                    </div>

                                    <div class="kt-widget__section float-right ml-5">
                                        <a href="index.php?option=<?php echo $page['list_page_name']; ?>"><button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold"><?= __("Details") ?></button></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }?>
            </div>
        </div>
    </div>
</div>

