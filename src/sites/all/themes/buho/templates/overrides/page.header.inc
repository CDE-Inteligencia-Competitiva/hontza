<?php // $Id$

/**
 * @file
 * Main template file
 *
 * @see template_preprocess_page(), preprocess/preprocess-page.inc
 * http://api.drupal.org/api/function/template_preprocess_page/6
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language->language; ?>" xml:lang="<?php print $language->language; ?>">
    <head>
      <?php print $head; ?>
      <title><?php print $head_title; ?></title>
      <meta name="description" content="Hontza, software, codigo abierto, open source, inteligencia competitiva, inteligencia estrategica, vigilancia tecnológica, competitive intelligence, strategic intelligence, technology watch, veille technologique, intelligence competititve, intelligence strategique">
      <meta name="keywords" content="Hontza, software, codigo abierto, open source, inteligencia competitiva, inteligencia estrategica, vigilancia tecnológica, competitive intelligence, strategic intelligence, technology watch, veille technologique, intelligence competititve, intelligence strategique">
      <?php print $styles; ?>
      <?php print $ie_styles; ?>
      <?php print  $scripts; ?>
    </head>
  <?php //gemini-2014?>    
  <?php $body_attributes=my_get_body_attributes($left,$body_attributes);?>    
  <body<?php print $body_attributes; ?>>
  <?php if (!empty($admin)) print $admin; // support for: http://drupal.org/project/admin ?>
  <div id="<?php print hontza_canal_rss_get_wrapper_id();?>">
   <div<?php print $header_attributes; ?>> 
        <?php if(red_is_iframe()):?>
            <?php $logo='';?>
        <?php endif;?>
       
        <div id="uno">
        <?php if ($site_slogan): ?>
          <div id="site-slogan"><?php print $site_slogan; ?></div>
        <?php endif; ?>
        <?php //gemini-2014?>  
        <?php $logo=alerta_get_introduccion_logo_by_subdominio(0,$logo);?>  
        <?php if ($logo): ?>
        <?php //gemini-2014?>  
        <?php //$logo=alerta_get_introduccion_logo_by_subdominio(0,$logo);?>  
        <a href="<?php print $front_page; ?>" title="<?php print $site_name; ?>" id="logo"><img src="<?php print $logo; ?>" alt="<?php if ($site_name): print $site_name;  endif; ?>" /></a>
        <?php else:?>
          <a href="<?php print $front_page; ?>" title="<?php print $site_name; ?>" id="logo"><div style="width:250px;">&nbsp;</div></a>        
        <?php endif; ?>
        <?php if(hontza_is_sareko_id_red()):?>
            <?php print red_get_red_link_sin_grupo();?>
        <?php endif;?>
        </div>
       <div id="dos">
         <div id="menutop"><?php print $menuuser; ?></div>
       </div>
      <div id="tres">
        <?php if ($nombredelgrupo && !is_user_invitado()): ?>	
		 <?php print my_resto_grupos_li();?>
		 <?php //print my_resto_grupos_html();?>		 
		<?php //gemini ?>
		<?php else:?>
			<?php print my_resto_grupos_li();?>						
		<?php endif; ?>
       </div>
       
    </div>
    <?php $menutop=hontza_set_menutop_by_group($menutop);?>
    <?php $menutop=my_get_menutop($menutop);?>
    <?php $menutop=hontza_navigation_menutop_page_header($menutop,1);?>
    <?php if ($menutop): ?>
      <?php $menutop=hontza_set_menutop_monitoring_default($menutop);?>
      <?php $menutop=red_funciones_set_menutop_active_trail($menutop);?>  
      <?php //gemini ?>
      <?php if(is_user_invitado()):?>
        <?php print hontza_navigation_menutop_page_header($menutop);?>
      <?php else:?>
      <?php //modo origanala else adarrekoa da?>
      <div id="navigation">
        <?php //print my_fix_menutop($menutop); ?>
        <?php print hontza_grupos_set_active_menutop($menutop); ?>
      </div>
      <?php endif;?>
    <?php else:?>
      <?php $menutop=red_funciones_set_menutop_active_trail($menutop);?>  
      <?php print hontza_navigation_menutop_page_header($menutop);?>
    <?php endif; ?>
<div id="color" class="azul"></div>
    <div id="color" class="azul-2"></div>
    <div id="container" class="layout-region">        