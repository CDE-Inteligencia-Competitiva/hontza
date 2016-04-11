<?php
// $Id: page.tpl.php,v 1.1.2.1 2010/06/17 07:54:57 sociotech Exp $
?><!DOCTYPE html>
<html lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>">

<head>
  <title><?php print $head_title; ?></title>
  <meta name="apple-mobile-web-app-capable" content="yes" /> 
  <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=0;" /> 
  <!-- For iPhone 4 with high-resolution Retina display: -->
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="apple-touch-icon-114x114-precomposed.png">
  <!-- For first-generation iPad: -->
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="apple-touch-icon-72x72-precomposed.png">
  <!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->
  <link rel="apple-touch-icon-precomposed" href="apple-touch-icon-precomposed.png">
  <?php print $head; ?>
  <?php print $styles; ?>
  <?php print $setting_styles; ?>
  <?php print $local_styles; ?>
  <?php print $scripts; ?>
</head>

<body id="<?php print $body_id; ?>" class="<?php print $body_classes; ?>">
  <div id="page" class="page">
    <div id="skip">
      <a href="#main-content-area"><?php print t('Skip to Main Content Area'); ?></a>
    </div>

    <div id="header-group" class="header-group row clearfix <?php print $grid_width; ?>">
      <?php print theme('grid_block', $header, 'header'); ?>
      <?php //print theme('grid_block', $search_box, 'search-box'); ?>

      <?php //intelsat-2015?>
      <?php //$logo=red_movil_get_logo($logo);?>
      <?php $logo='';?>  
      <?php $site_name=red_movil_get_site_name($site_name);?>            
      <?php if ($logo || $site_name || $site_slogan): ?>
      <div id="header-site-info" class="header-site-info block">
        <div id="header-site-info-inner" class="header-site-info-inner inner">
          <?php if ($logo): ?>
          <div id="logo">
            <a href="<?php print check_url($front_page); ?>" title="<?php print t('Home'); ?>"><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" /></a>
          </div>
          <?php endif; ?>
          <?php if ($site_name || $site_slogan): ?>
          <div id="site-name-wrapper" class="clearfix">
            <?php if ($site_name): ?>
            <span id="site-name"><a href="<?php print check_url($front_page); ?>" title="<?php print t('Home'); ?>"><?php print $site_name; ?></a></span>
            <?php endif; ?>
            <?php if ($site_slogan): ?>
            <span id="slogan"><?php print $site_slogan; ?></span>
            <?php endif; ?>
          </div><!-- /site-name-wrapper -->
          <?php endif; ?>
        </div><!-- /header-site-info-inner -->
      </div><!-- /header-site-info -->
      <?php endif; ?>
    </div><!-- /header-group -->

    <!-- add primary menu as a block region so mobile site can have different links -->
    <?php if ($primary_menu): ?>
    <div id="primary-menu" class="primary-menu row <?php print $grid_width; ?>">
      <?php print $primary_menu; ?>
    </div><!-- /primary_menu -->
    <?php endif; ?>

    <div id="main" class="main row clearfix <?php print $grid_width; ?>">
      <div id="main-group" class="main-group row nested <?php print $main_group_width; ?>">
        <div id="content-group" class="content-group row nested <?php print $content_group_width; ?>">
          <?php //intelsat-2015?>
          <?php print theme('grid_block', red_movil_get_user_menus(),'custom-user-menus'); ?>              
          <?php print theme('grid_block', $breadcrumb, 'breadcrumbs'); ?>
          <?php print theme('grid_block', $help, 'content-help'); ?>
          <?php print theme('grid_block', $messages, 'content-messages'); ?>
          <a name="main-content-area" id="main-content-area"></a>
          <?php print theme('grid_block', $tabs, 'content-tabs'); ?>

          <div id="content-inner" class="content-inner block">
            <div id="content-inner-inner" class="content-inner-inner inner">
              <?php //intelsat-2015?>  
              <?php $title=red_movil_get_title($title);?>    
              <?php if ($title): ?>
              <h1 class="title"><?php print $title; ?></h1>
              <?php endif; ?>
    
              <?php if ($content): ?>
              <div id="content-content" class="content-content">
                <?php print $content; ?>
              </div><!-- /content-content -->
              <?php endif; ?>
            </div><!-- /content-inner-inner -->
          </div><!-- /content-inner -->
        </div><!-- /content-group -->
      </div><!-- /main-group -->
    </div><!-- /main -->

    <?php if ($footer): ?>
    <div id="footer" class="footer row <?php print $grid_width; ?>">
      <?php print $footer; ?>
    </div><!-- /footer -->
    <?php endif; ?>

    <?php if ($footer_message): ?>
    <div id="footer-message" class="footer-message row <?php print $grid_width; ?>">
      <?php //intelsat-2015?>
      <?php //print theme('grid_block', $footer_message, 'footer-message-text'); ?>
      <?php print theme('grid_block', red_movil_get_footer_message_powered(), 'footer-message-text'); ?>  
    </div><!-- /footer-message -->
    <?php endif; ?>
  </div><!-- /page -->
  <?php print $closure; ?>
</body>
</html>
