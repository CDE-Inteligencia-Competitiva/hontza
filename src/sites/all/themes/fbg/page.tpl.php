<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language ?>" lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">
<head>
  <!--
  *****************************************************************
    FBG - Fluid Baseline Grid - http://drupal.org/project/fbg
	FGB Drupal 6 theme by Jason More and Arbor Web Development, http://arborwebdev.com.
	FBG Drupal Theme is based on Fluid Baseline Grid v1.0.1.
    Fluid Baseline Grid was Designed & Built by Josh Hopkins and 40 Horse, http://40horse.com.
    FBG is Licensed under Unlicense, http://unlicense.org/
  *****************************************************************
  -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
  <!-- Optimized mobile viewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Place favicon.ico and apple-touch-icon.png in root directory -->
  <?php print $head ?>
  <title><?php print $head_title ?></title>
  <?php print $styles ?>
  <?php print $scripts ?>
  <!--[if lt IE 7]>
  <?php print phptemplate_get_ie_styles(); ?>
  <![endif]-->
</head>
<body<?php print phptemplate_body_class($left, $right); ?>>
  <header>
    <?php if (!empty($header_top)): ?>
	  <div id="header-top">
		<?php print $header_top; ?>
	  </div>
	<?php endif; ?>
	<div id="header">
	  <?php if (!empty($header)): ?>
	    <div class="g3">
		  <?php print $header; ?>
	    </div>
	    <div class="cf"></div>
	  <?php endif; ?>
	  <div class="g3">
        <?php if (!empty($site_name)): ?>
            <?php if ($is_front): /* Use h1 when on front page */ ?>
			  <h1 id="site-name">
                <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
              </h1>
            <?php else:  ?>
              <div id="site-name">
			    <strong><a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a></strong>
		      </div>
            <?php endif; ?>
          <?php endif; ?>
      </div>
	  <?php if (!empty($site_slogan)): ?>
	    <div class="g3">
	      <h2 id="site_slogan"><?php print $site_slogan ?></h2>
	    </div>
	  <?php endif; ?>
	</div><!-- /#header -->
	<div class="cf"></div>
  </header>
  <?php if (!empty($primary_links)): ?>
    <nav>
      <div id="primary-menu" class="g3 nav">
        <?php print theme('links', $primary_links, array('class' => 'links primary-links')); ?>
      </div><!-- /end #primary-menu -->
	</nav>
	<div class="cf"></div>
  <?php endif; ?>
  <section id="section-content" class="section section-content">
	<div id="page">
	  <?php if (!empty($highlighted) || !empty($mission)): ?>
	    <div class="g2">
	      <?php print $highlighted; ?>
	    </div>
      <?php endif; ?>
	  <?php if (!empty($branding)): ?>
	    <div class="g1">
		  <?php print $branding; ?>
	    </div>
	  <?php endif; ?>
      <div class="cf"></div>
      <?php if (!empty($tryptych_top1)): ?>
	    <div class="g1">
	      <?php print $tryptych_top1; ?>
	    </div>
      <?php endif; ?>
      <?php if (!empty($tryptych_top2)): ?>
        <div class="g1">
	      <?php print $tryptych_top2; ?>
	    </div>
      <?php endif; ?>
      <?php if (!empty($tryptych_top3)): ?>
	    <div class="g1">
	      <?php print $tryptych_top3; ?>
	    </div>
      <?php endif; ?>
      <div class="cf"></div>
      <?php if (!empty($highlighted_2)): ?>
	    <div class="g2">
	      <?php print $highlighted_2; ?>
	    </div>
      <?php endif; ?>
      <?php if (!empty($branding2)): ?>
	    <div class="g1">
	      <?php print $branding2; ?>
	    </div>
      <?php endif; ?>
      <?php if (!empty($left)): ?>
	    <aside id="region-sidebar-first" class="region region-sidebar-first">
	      <div id="sidebar1" class="g1">
	        <?php print $left; ?>
	      </div>
		</aside><!-- /#region-sidebar-first -->
      <?php endif; ?>
      <div id="main" <?php if (!empty($left) xor !empty($right)): ?>class="g2"<?php endif; ?> <?php if (!empty($left) && !empty($right)): ?>class="g1"<?php endif; ?>>
	    <?php if (!empty($content_top)): ?>
	      <div class="g3">
		    <?php print $content_top; ?>
	      </div>
	    <?php endif; ?>
	    <div class="g3">
		  <div id="content">
		    <?php if (!empty($breadcrumb)): ?><div id="breadcrumb"><?php print $breadcrumb; ?></div><?php endif; ?>
            <?php if (!empty($mission)): ?><div id="mission"><?php print $mission; ?></div><?php endif; ?> 
			<?php if (!empty($title)): ?><h1 class="title" id="page-title"><?php print $title; ?></h1><?php endif; ?>
            <?php if (!empty($tabs)): ?><div class="tabs"><?php print $tabs; ?></div><?php endif; ?>
            <?php if (!empty($messages)): print $messages; endif; ?>
            <?php if (!empty($help)): print $help; endif; ?> 
            <div id="content-content" class="clear-block">
              <?php print $content; ?>
            </div> <!-- /content-content --> 
            <?php print $feed_icons ?>
          </div><!--#content -->		  
	    </div>
	    <?php if (!empty($content_bottom)): ?>
	      <div class="g3">
	        <?php print $content_bottom; ?>
	      </div>
	    <?php endif; ?>  
      </div><!-- /#main -->
      <?php if (!empty($right)): ?>
        <aside id="region-sidebar-second" class="region region-sidebar-second">
	      <div id="sidebar2" class="g1">
	        <?php print $right; ?>
	      </div>
	    </aside><!-- /#region-sidebar-second -->
      <?php endif; ?>
      <div class="cf"></div>
      <?php if (!empty($tryptych_bottom1)): ?>
	    <div class="g1">
	      <?php print $tryptych_bottom1; ?>
	    </div>
      <?php endif; ?>
      <?php if (!empty($tryptych_bottom2)): ?>
	    <div class="g1">
	      <?php print $tryptych_bottom2; ?>
	    </div>
      <?php endif; ?>
      <?php if (!empty($tryptych_bottom3)): ?>
	    <div class="g1">
	      <?php print $tryptych_bottom3; ?>
	    </div>
      <?php endif; ?>
    </div><!-- /#page -->
  </section><!-- /section -->
  <?php if (!empty($footer) || !empty($footer_message)): ?>
	<footer>
	  <div id="footer" class="g3">
	    <?php if (!empty($footer)): ?>
	      <?php print $footer; ?>
	    <?php endif; ?>
	    <?php if (!empty($footer_message)): ?>
	      <div id="footer-message">
	        <?php print $footer_message; ?>
	      </div>
		<?php endif; ?>
	  </div>
	</footer>
  <?php endif; ?>
<!-- JavaScript at the bottom for fast page loading -->
<!-- Minimized jQuery from Google CDN -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery-ui/1.8.21/jquery-ui.min.js"></script>
<!-- HTML5 IE Enabling Script -->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!-- CSS3 Media Queries -->
<script src="<?php print path_to_theme();?>/js/respond.min.js"></script>
<!-- Optimized Google Analytics. Change UA-XXXXX-X to your site ID -->
<script>var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.src='//www.google-analytics.com/ga.js';s.parentNode.insertBefore(g,s)}(document,'script'))</script>
</body>
  <?php print $closure ?>
</html>