<?php // $Id$

/**
 * @file
 * Main template file
 *
 * @see template_preprocess_page(), preprocess/preprocess-page.inc
 * http://api.drupal.org/api/function/template_preprocess_page/6
 */
?>
<?php if(!red_crear_usuario_is_custom_css()):?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language->language; ?>" xml:lang="<?php print $language->language; ?>">
    <head>
      <?php print $head; ?>
      <title><?php print $head_title; ?></title>
      <meta name="description" content="Hontza, software, codigo abierto, open source, inteligencia competitiva, inteligencia estrategica, vigilancia tecnológica, competitive intelligence, strategic intelligence, technology watch, veille technologique, intelligence competititve, intelligence strategique">
      <meta name="keywords" content="Hontza, software, codigo abierto, open source, inteligencia competitiva, inteligencia estrategica, vigilancia tecnológica, competitive intelligence, strategic intelligence, technology watch, veille technologique, intelligence competititve, intelligence strategique">
      <?php if(hontza_canal_rss_is_visualizador_activado()):?>
      <?php if($publico_title):?>    
      <meta property="og:title" content="<?php print $publico_title;?>" />
      <meta property="og:description" content="<?php print $publico_description;?>" />
      <?php endif;?>
      <?php endif;?>
      <?php print $styles; ?>
      <?php print $ie_styles; ?>
      <?php print $scripts; ?>  
    </head>
  <?php //gemini ?>
  <?php $left=my_get_sidebar_left($left);?>      
  <?php $body_attributes=my_get_body_attributes($left,$body_attributes);?>
  <body<?php print $body_attributes; ?>>    
  <?php if (!empty($admin)) print $admin; // support for: http://drupal.org/project/admin ?>
  <div id="<?php print hontza_canal_rss_get_wrapper_id();?>"<?php print red_despacho_get_wrapper_style();?>>
       <div<?php print $header_attributes; ?>>         
        <div id="uno">
        <?php if ($site_name): ?>
        <!--    
        <div id="site-name"> <a href="<?php //print $front_page; ?>" title="<?php //print $site_name; ?>"><?php //print $site_name; ?></a> </div>
        -->
        <?php endif; ?>
        <?php //gemini-2014?>  
        <?php $logo=alerta_get_introduccion_logo_by_subdominio(0,$logo);?> 
        <?php if(red_is_iframe()):?>
            <?php $logo='';?>
        <?php endif;?>
        <?php if(!hontza_is_tag_node_pantalla()):?>
        <?php if ($site_slogan): ?>
          <div id="site-slogan"><?php print $site_slogan; ?></div>
        <?php endif; ?>
        <?php if ($logo): ?>
        <?php //gemini-2014?>
        <?php //$logo=alerta_get_introduccion_logo_by_subdominio(0,$logo);?>  
          <a href="<?php print $front_page; ?>" title="<?php print $site_name; ?>" id="logo"><img <?php print $logo_style;?>src="<?php print $logo; ?>" alt="<?php if ($site_name): print $site_name;  endif; ?>" /></a>
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
       <?php endif;?><!-- !hontza_is_tag_node_pantalla() && !red_is_iframe()-->
       
    </div>
    <?php $menutop=hontza_set_menutop_by_group($menutop);?>
    <?php $menutop=my_get_menutop($menutop);?>
    <?php $menutop=hontza_navigation_menutop_page_header($menutop,1);?>  
    <?php if ($menutop):?>  
    <?php $menutop=hontza_set_menutop_monitoring_default($menutop);?>
    <?php endif;?>
    <?php $menutop=red_funciones_set_menutop_active_trail($menutop);?>
    <?php if ($menutop && !is_user_invitado()): ?>
      <?php //intelsat-2015?>
      <?php //$menutop=hontza_canal_rss_set_menutop_publico_vigilancia_sf_js_enabled($menutop);?>
      <?php if(!hontza_is_tag_node_pantalla()):?> 
      <div id="navigation">
        <?php //print my_fix_menutop($menutop); ?>
        <?php if(is_usuarios_menu()):?>
        <?php   $menutop=my_get_menutop($menutop);?>
        <?php endif;?>
        
        <?php print hontza_grupos_set_active_menutop($menutop); ?>
        
      </div>
      <?php endif;?>
	<?php //gemini?>
	<?php else:?>
	<?php //gemini?>
        
	<?php $menutop=my_get_menutop($menutop);?>
        <?php if(!hontza_is_tag_node_pantalla()):?>
        <div id="navigation">    
		<a id="context-block-region-menutop" class="context-block-region"></a>
        <div class="block block-menu block-even region-odd clearfix style-menu " id="block-menu-primary-links">
			<h3 class="title"><?php t('Primary links');?></h3>
			<div class="content">
				<ul class="menu sf-js-enabled">
					<?php print hontza_grupos_set_active_menutop($menutop);;?>
				</ul>
				<a class="context-block editable edit-vigilancia" id="context-block-menu-primary-links"></a>  
			</div>
		</div>
	</div>
        <?php endif; ?>
    <?php endif; ?>
      
<div id="color" class="azul"></div>
    <div id="color" class="azul-2"></div>
    <div id="container" class="layout-region">      
       <?php //gemini-2014?>
       <?php if(!hontza_is_tag_node_pantalla()):?>       
       <?php if ($left): ?>
        <div id="sidebar-left" class="sidebar">
          <div class="inner">
            <?php print $left; ?>
          </div>
        </div>
      <!-- END HEADER -->
      <?php endif; ?>
      <?php endif; ?>
      
      <div id="main"<?php print hontza_canal_rss_get_div_main_style();?>>
        <div class="main-inner"<?php print red_solr_inc_get_div_main_inner_style();?>>          
          <?php if ($show_messages && $messages != ""): ?>
          <?php print $messages; ?>
          <?php endif; ?>
          <?php if ($is_front && $mission): ?>
            <div class="mission"><?php print $mission; ?></div>
          <?php endif; ?>
          <?php if ($contenttop): ?>
            <div id="content-top"><?php print $contenttop; ?></div>			
            <!-- END CONTENT TOP -->
          <?php endif; ?>
          	  <?php my_show_breadcrumb($breadcrumb); ?>
                  <?php //gemini-2014?>
                  <?php if(!hontza_is_tag_node_pantalla()):?>
                  <?php if ($title): ?>
                        <?php //intelsat-2015 ?>
                        <?php $title=hontza_solr_search_get_solr_pantalla_title($title);?>
                        <?php $title_in=$title;?>
                        <?php $title=red_funciones_set_title_help_icon($title);?>
            
                                <?php //if(is_idea()):?>
                                <?php if(is_ficha_node('oportunidad') || is_oportunidad() || oportunidad_is_ideas_oportunidades()):?>
                                    <div style="float:left;">
                                        <?php print get_title_oportunidad_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla">&nbsp;<?php print $title; ?></h1>

                                 <?php elseif(is_ficha_node('proyecto') || is_proyecto() || proyecto_is_ideas_proyectos()):?>
                                    <div style="float:left;">
                                        <?php print get_title_proyecto_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla<?php print red_copiar_get_title_imported_class();?>">&nbsp;<?php print $title; ?></h1>
                                 <?php elseif(is_ficha_node('idea') || is_idea()):?>
                                    <div style="float:left;">
                                        <?php print get_title_idea_bombilla_img();?>
                                    </div>
                                    <h1 class="title bombilla">&nbsp;<?php print $title; ?></h1>                                 
                                 <?php elseif(estrategia_inc_is_estrategia_main()):?>
                                    <div style="float:left;">
                                        <?php print estrategia_inc_get_title_estrategia_main_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla<?php print red_copiar_get_title_imported_class();?>">&nbsp;<?php print $title; ?></h1>
                                  <?php elseif(is_ficha_node('estrategia') || is_estrategia()):?>
                                    <div style="float:left;">
                                        <?php print get_title_estrategia_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla<?php print red_copiar_get_title_imported_class();?>">&nbsp;<?php print $title; ?></h1>
                                  <?php elseif(is_ficha_node('despliegue') || is_despliegue()):?>
                                    <div style="float:left;">
                                        <?php print get_title_despliegue_simbolo_img()?>
                                    </div>
                                    <h1 class="title bombilla">&nbsp;<?php print $title; ?></h1>
                                  <?php elseif(is_ficha_node('decision') || is_decision()):?>
                                    <div style="float:left;">
                                        <?php print get_title_decision_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla">&nbsp;<?php print $title; ?></h1>
                                  <?php elseif(is_ficha_node('informacion') || is_informacion()):?>
                                    <div style="float:left;">
                                        <?php print get_title_informacion_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla">&nbsp;<?php print $title; ?></h1>
                                   <?php elseif(is_ficha_node('supercanal') || is_ficha_node('fuentedapper') || is_ficha_node('fuentehtml') || red_fuente_is_compartir_fuente_servidor(1) || is_fuentes()):?>
                                    <div style="float:left;">
                                        <?php print hontza_get_title_fuente_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla<?php print red_copiar_get_title_imported_class();?>">&nbsp;<?php print $title; ?></h1>
                                    <?php elseif(boletin_report_inc_is_boletin_report() || is_ficha_node('my_report')):?>
                                    <div style="float:left;">
                                        <?php print boletin_report_inc_get_boletin_personalizado_title_simbolo_img($title_in);?>
                                    </div>
                                    <h1 class="title bombilla<?php print red_copiar_get_title_imported_class();?>">&nbsp;<?php print $title; ?></h1>                                    
                                    <?php elseif(boletin_grupo_is_boletin_grupo()):?>
                                    <div style="float:left;">
                                        <?php print boletin_grupo_get_title_simbolo_img($title_in);?>
                                    </div>
                                    <h1 class="title bombilla">&nbsp;<?php print $title; ?></h1>                                    
                                    <?php elseif(is_ficha_node('wiki') || is_area_trabajo()):?>
                                    <div style="float:left;">
                                        <?php print hontza_get_title_wiki_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla<?php print red_copiar_get_title_imported_class();?>">&nbsp;<?php print $title; ?></h1>
                                    <?php elseif(is_ficha_node('debate') || is_area_debate()):?>
                                    <div style="float:left;">
                                        <?php print hontza_get_title_debate_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla">&nbsp;<?php print $title; ?></h1>
                                    <?php elseif(is_ficha_node('canal_de_supercanal') || is_ficha_node('canal_de_yql') || red_canal_is_compartir_canal_servidor(1) || is_ficha_node('canal_usuario')):?>
                                    <div style="float:left;">
                                        <?php print hontza_get_title_canal_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla<?php print red_copiar_get_title_imported_class('',$title);?>">&nbsp;<?php print $title; ?></h1>
                                    <?php elseif(hontza_solr_is_resultados_pantalla()):?>
                                    <div style="float:left;">
                                        <?php //print hontza_solr_funciones_get_title_volver_simbolo_img();?>
                                        <?php print hontza_get_title_canal_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla">&nbsp;<?php print $title; ?></h1>
                                    <?php elseif(is_fuentes_pipes_todas()):?>
                                    <div style="float:left;">
                                        <?php print hontza_get_title_fuente_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla">&nbsp;<?php print $title; ?></h1>
                                    <?php elseif(hontza_grupos_mi_grupo_is_mi_grupo() || hontza_is_mis_grupos()):?>
                                    <div style="float:left;">
                                        <?php print hontza_grupos_mi_grupo_get_title_grupo_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla">&nbsp;<?php print $title; ?></h1>
                                    <?php elseif(is_vigilancia()):?>
                                    <div style="float:left;">
                                        <?php print hontza_get_title_canal_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla<?php print red_copiar_get_title_imported_class();?>">&nbsp;<?php print $title; ?></h1>
                                    <?php elseif(alerta_inc_is_configuracion()):?>
                                    <div style="float:left;">
                                        <?php print alerta_inc_get_title_configuracion_simbolo_img($title_in);?>
                                    </div>
                                    <h1 class="title bombilla">&nbsp;<?php print $title; ?></h1>
                                    <?php elseif(is_alerta_user()):?>
                                    <div style="float:left;">
                                        <?php print alerta_inc_get_title_alerta_user_simbolo_img();?>
                                    </div>
                                    <h1 class="title bombilla">&nbsp;<?php print $title; ?></h1>
                                    <?php elseif(hontza_canal_rss_is_visualizador_inicio()):?>
                                        <?php //no title?>
                                <?php else:?>
                                    <h1 class="title<?php print red_copiar_get_title_imported_class();?>"><?php print $title; ?></h1>
                                <?php endif;?>
                                <?php //gemini ?>
                                <!--    
				<div style="float:right;">
				<?php //print help_popup_window(2354, 'help');?>
				<?php //print my_show_help_icon();?>
				</div>
                                -->
		  <?php endif; ?>
		  <?php //gemini?>
		  <?php if($my_volver_link):?>
		  	<?php print $my_volver_link;?>
		  <?php endif;?>
                  <?php endif;?>
          
            <?php if(red_funciones_is_show_menu_ideas_by_page_tpl()):?>
             <div class="clearfix" style="clear:both;">                 
             <?php print create_menu_ideas();?>
             </div>
            <?php endif;?>                        
                                    
          <?php if ($help): ?>
            <div class="help"><?php print $help; ?></div>
          <?php endif; ?>
          <?php print $tabs; ?>
          <?php if(hontza_is_area_debate_node_list()):?>
                <?php print hontza_area_debate_create_menu_node_list();?>
          <?php elseif(hontza_is_area_trabajo_node_list()):?>  
                <?php print hontza_area_trabajo_create_menu_node_list();?>          
          <?php endif;?>        
            
		  <?php //gemini ?>
		  <!--
          <div id="content" class="clearfix">
		  -->
          <div id="content" class="clearfix" style="clear:both;">
                    <?php print $content; ?>
          </div>
          <!-- END CONTENT -->
          <?php print $feed_icons; ?>
          <?php if ($contentbottom): ?>
            <div id="content-bottom"><?php print $contentbottom; ?></div>
          <?php endif; ?>
        </div>
        <!-- END MAIN INNER -->
      </div>
      <?php if(hontza_canal_rss_is_publico_activado()):?>
        <?php if(publico_is_pantalla_publico('publico_comment') && !publico_is_validate_email_reply()):?>
        </div>
        <?php endif;?>
      <?php endif;?>  
      <!-- END MAIN -->
      <?php $right=hontza_canal_rss_get_sidebar_right($right);?>
      <?php if ($right): ?>
        <div id="sidebar-right" class="sidebar"<?php print hontza_canal_rss_get_div_sidebar_right_style();?>>
          <div class="inner">
          <?php print $right; ?>
          </div>
        </div>
      <!-- END SIDEBAR RIGHT -->
      <?php endif; ?>
    </div>
    <!-- END CONTAINER -->
    <?php //if(!hontza_is_tag_node_pantalla()):?> 
    <?php if(hontza_canal_rss_is_add_push_div()):?>
    <div class="push">&nbsp;</div>
    <?php endif;?>
  </div>
  <!-- END WRAPPER -->
  <?php if(!hontza_is_tag_node_pantalla()):?> 
  <?php if(hontza_is_footer()):?>
  <div id="footer" class="layout-region" style="width:960px;margin: 0 auto -1em;">
    <?php print hontza_canal_rss_get_logos_apis();?>      
    <?php //intelsat-2015?>
    <!--  
    <div id="footer-inner">
    -->
    <div id="footer-inner" class="footer-inner-integrated-services" style="padding-top:20px;">  
      <?php print $contentfooter; ?>
      <?php //gemini ?>
      <?php //print $footer_message; ?>
      <?php //print 'Powered by Hontza 3.0.';?>
      <?php print get_frase_powered('castellano');?>
        <BR>
      <?php print get_frase_powered('ingles');?>
    </div>
  </div>
  <?php else:?>
  
  <div id="footer" class="layout-region" style="width:960px;margin: 0 auto -1em;">
    <?php //print hontza_canal_rss_get_logos_apis();?>  
    <div id="footer-inner-light">
      <?php print $contentfooter; ?>
      <?php //gemini ?>
      <?php //print $footer_message; ?>
      <?php //intelsat-2015?>
      <?php //se ha comentado?>  
      <?php print hontza_get_frase_powered_light();?>       
    </div>
  </div>
  <?php endif;?>
  <?php endif;?>
  <?php print $closure; ?>
  </body>
</html>
<?php elseif(red_crear_usuario_is_custom_css_hontza() && !red_is_subdominio_alerta()):?>
    <?php require_once('page.custom.css.hontza.tpl.php');?>
<?php else:?>
    <?php require_once('page.custom.css.tpl.php');?>
<?php endif; ?>