<?php
// $Id: node.tpl.php,v 1.5 2010/06/06 09:51:29  Exp $
?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?><?php print my_class_primera_noticia($node,$page);?>">
  <?php //gemini ?>
  <?php $my_user_info=my_get_user_info($node);?>
  <?php if ($page == 0):?>
    <?php if(red_funciones_is_vista_compacta()):?>
      <?php include('item-vista-compacta.tpl.php');?>
    <?php else:?>
    <div id="flagtitulo">
        <?php if(hontza_solr_funciones_is_bookmark_activado() && !(hontza_solr_search_is_usuario_lector())):?>
            <?php print hontza_solr_funciones_get_node_bookmark_checkbox_html($node);?>
        <?php endif;?>
        <?php //intelsat-2015 ?>
        <?php if(!hontza_solr_search_is_usuario_lector()):?>
        <div class="f-interesante"><?php print hontza_canal_rss_set_flag_link($node->links['flag-leido_interesante']['title']); ?></div>
        <div class="f-no-interesante"><?php print hontza_canal_rss_set_flag_link($node->links['flag-leido_no_interesante']['title']); ?></div>
        <?php endif;?>
		<!-- gemini -->
		<!--
        <div class="f-titulo"> <h2><a href="<?php //print $node_url ?>" title="<?php //print $title ?>"><?php //print $title ?></a></h2></div>
		-->
                <!--
		<div class="f-titulo"> <h2><?php //print l(htmlkarakter($title),'node/'.$node->nid);?></h2></div>
                -->
        <?php $node_url=hontza_canal_rss_get_node_url($node);?>
      <?php if(hontza_canal_rss_is_visualizador_activado()):?>
        <?php if(visualizador_is_pantalla('inicio')):?>
      <div class="f-titulo"><h5<?php print red_copiar_get_title_imported_class($node);?>><b><?php print l(htmlkarakter($title),$node_url,array('query'=>  drupal_get_destination(),'attributes'=>array('target'=>'_blank')));?></b></h5></div>
        <?php else:?>
            <div class="f-titulo"><h2<?php print red_copiar_get_title_imported_class($node);?>><?php print l(htmlkarakter($title),$node_url,array('query'=>  drupal_get_destination()));?></h2></div>
        <?php endif;?>
      <?php else:?>
      <div class="f-titulo"><h2<?php print red_copiar_get_title_imported_class($node);?>><?php print l(htmlkarakter($title),$node_url,array('query'=>  drupal_get_destination()));?></h2></div>
      <?php endif;?>        
    	<?php print my_help_primera_noticia($node);?>
	</div>	
    
	
	
	
	<!--
	<div style="width:100%;">
	<b><?php //print $my_user_info['username'];?></b>
  	</div>
	-->
    
	<div style="clear:both;width:100%;min-width:75px;">
        <?php //if(hontza_canal_rss_is_show_user_image()):?>
        <?php if(red_despacho_is_show_user_image()):?> 	    
	<div style="float:left;">
			<!--
			<img title="admin's picture" alt="admin's picture" src="http://92.243.10.49/hontza3/sites/default/files/pictures/picture-1.jpg">
			-->		
			<?php print $my_user_info['img'];?> 
	</div>
        <?php endif;?>    
        <div id="i-contenedor">
                <?php if(hontza_node_has_body($node)): ?>
                <div class="noticias-teaser-texto">
                        <?php print hontza_content_resumen($node);?>                
                </div>
                <?php endif; ?>
                <div id="ffc">        
                    <?php if(!hontza_canal_rss_is_show_user_image('inicio')):?>
                    <?php if(!hontza_solr_search_is_usuario_lector()):?> 
			 	<div class="item-fecha">
                                    <?php print hontza_canal_rss_get_username_item($my_user_info);?>
                                    <?php //print date('d/m/Y',$node->created); ?>
                                    <?php print hontza_get_item_fecha_created($node);?>
                                </div>
                    <?php endif;?>
                    <?php else:?>
                        <?php if(!hontza_is_user_anonimo()):?>
                            <div class="item-fecha">
                                <?php print hontza_get_item_fecha_created($node);?>
                            </div>
                        <?php endif;?>
                    <?php endif;?>
                    <?php if(hontza_canal_rss_is_show_user_image()):?>
                    <?php //gemini-2014?>
                    <div class="item-canal">
                        <span class="etiqueta-gris"><?php print t('Channel');?>: </span> <?php print l(hontza_get_canal_usuarios_title($node->uid),'canal_usuario/'.$node->uid.'/view'); ?>
                    </div>
                    <?php endif;?>
                </div>                
         <?php //Bloque de FiveStar y comentarios ?>
      <!--  
      <div class="item-fivestar">
	    <div style="float:left;">
        <?php //print $node->content['fivestar_widget']['#value'] ?>
		</div>
                -->
         <?php //Tags ?>
         <div id="ffc">          
          <div class="item-categorias"<?php print hontza_item_categorias_style();?>>
              <?php if(is_array($node->taxonomy)): ?>
              
  				<?php //gemini ?>			  
                <?php /*foreach($node->taxonomy as $etiqueta):?>
                  <?php print $etiqueta->name; ?>
                <?php endforeach; */?>
                <?php print get_resumen_etiqueta($node->taxonomy);?>
              <?php endif; ?>
           </div>
          </div>
          <?php //intelsat-2015 ?>
          <?php if(!hontza_is_user_anonimo()):?>                          		      
          <?php if(hontza_canal_rss_is_show_categorias_tipos_fuentes_item()):?>          
          <?php //Fin tags?>
          <?php if(red_despacho_is_noticia_usuario_source_type_show($node)):?>      
          <div id="ffc">   
            <div class="field-label-inline-first" style="float:left;"><?php print t('Source Type');?>:</div>
                <div style="margin-top:0px;float:left;" class="terms terms-inline">
                    <?php print hontza_solr_funciones_get_item_source_types($node);?>
                </div>
          </div>
          <?php endif;?>      
          <div id="ffc">
            <?php if(!hontza_canal_rss_is_show_user_image()):?>
                                <?php print hontza_canal_rss_get_username_item($my_user_info);?>
                                    <div class="item-fecha" style="float:left;width: auto;">
                                        <?php print date('d/m/Y',$node->created); ?>
                                    </div>
                         <?php endif;?> 
                         <?php //intelsat-2015 ?> 
                         <?php $label_categories=t('Thematic Categories');?>
                         <?php if(hontza_is_user_anonimo()):?>
                             <?php $label_categories=t('Categories');?>
            <?php endif;?>  
            <div class="field-label-inline-first" style="float:left;"><?php print $label_categories;?>:</div>
            <div style="margin-top:0px;float:left;" class="terms terms-inline">
                <?php print hontza_solr_search_get_noticia_categorias_tematicas_html($node);?>
            </div>
          </div>
          <?php endif;?>
          <?php endif;?>
          <?php //intelsat-2016?>
          <!--      
          <?php //if(hontza_crm_is_activado()):?>          
          <div id="ffc">
            <span class="etiqueta-gris"><?php //print t('News link type');?>:</span>
            <?php //print crm_exportar_get_news_link_type_label($node);?> 
          </div>
          <?php //endif;?>
          -->          
      </div>
    <?php //Fin de bloque de FiveStar y comentarios ?>               
      
   </div>
	

   <?php print my_get_node_files($node);?>	
   
    <div class="n-opciones-item">	        
        <div class="n-item-fuente">
            <?php print hontza_noticia_usuario_web_link($node);?>            
        </div>        
	
        <?php //intelsat-2015 ?>
        <?php if(!hontza_solr_search_is_usuario_lector()):?>                               
        <?php //Añadir tag ?>
          <div class="n-item-etiquetar">
              <?php print hontza_item_tag_link($node);?>
          </div>
        <?php //Fin añadir tag ?>
        
        <?php //Añadir comentario ?>
          <div class="n-item-comentar">
              <?php print hontza_item_comment_link($node);?>
          </div>
        <?php //Fin añadir comentario ?>
                                 
        <?php if(red_node_is_show_debate_link($node)):?>
        <?php //Enviar debate ?>
          <div class="n-item-debate">
		    <!-- gemini -->
			<!--
            <a href="<?php //print 'node/add/debate/'.$node->nid?>" title="Enviar a debate" target= "_blank">Enviar a &Aacute;rea de debate</a>
            -->
			<?php //print l(t('Discuss'),'node/add/debate/'.$node->nid,array('attributes'=>array('target'=>'_blank')));?>
                        <?php print hontza_node_add_debate_link($node);?>
		  </div>
        <?php //Fin enviar debate ?>
        <?php endif;?>
        
        <?php if(red_node_is_show_collaboration_link($node)):?>
        <?php //Enviar trabajo ?>
          <div class="n-item-trabajo">
		    <!-- gemini -->
			<!--
            <a href="<?php //print 'node/add/wiki/'.$node->nid ?>" title="Enviar a trabajo"target= "_blank">Enviar a &Aacute;rea de trabajo</a>
            -->
			<?php //print l(t('Collaborate'),'node/add/wiki/'.$node->nid,array('attributes'=>array('target'=>'_blank')));?>
                        <?php print hontza_node_add_wiki_link($node);?>
		  </div>
        <?php //Fin enviar trabajo ?>
        <?php endif;?>
		
          <?php if(red_node_is_show_idea_link($node)):?>  
          <div class="n-item-crear_idea">
            <?php //print l(t('Idea'),'node/add/idea/'.$node->nid);?>
            <?php print idea_node_add_link($node);?>  
          </div>
          <?php endif;?>  

          <!--
          <div class="n-item-destacar">
            <?php //print l(t('Publish'),'publicar_noticia_usuario/'.$node->nid);?>
            <?php //print get_noticia_publicar_link($node);?>
          </div>
          -->
          
          <?php if(is_show_destacar_link()):?>
          <div class="<?php print hontza_noticia_destacar_action_class($node);?>">
            <?php //print l(t('Highlight'),'destacar_noticia_usuario/'.$node->nid);?>
            <?php print get_noticia_destacar_link($node);?>
          </div>
          <?php endif;?>
          
          <?php if(boletin_report_is_report_access($node)):?>
            <div class="<?php print boletin_report_action_class($node);?>">
                <?php print boletin_report_link($node);?>
            </div>
          <?php endif;?>
          
          <?php //intelsat-2015 ?>
          <?php if(boletin_report_is_save_noticia_boletines_sareko_id()):?>
            <div class="n-item-noticia-boletines">
                <?php print boletin_report_inc_noticia_usuario_boletines_link($node);?>
            </div>
          <?php endif;?>
          
        
            <?php if(red_despacho_is_show_categorizar_link()):?>
            <div class="n-item-tipo-fuente">
                <?php print red_despacho_get_reclasificar_tipo_fuente_link($node);?>
            </div>
            <?php endif;?>
            <?php if (red_despacho_is_instalado()):?>
            <div class="n-item-categorizar">
                <?php print despacho_vigilancia_get_categorizar_link($node);;?>
            </div>
            <?php endif;?>
            
           

          
          <?php if(hontza_solr_funciones_is_bookmark_activado()):?>
            <div class="<?php print hontza_solr_funciones_bookmark_action_class($node);?>">
                <?php print hontza_solr_funciones_bookmark_link($node);?>
            </div>
          <?php endif;?> 
          
          <?php //intelsat-2016?>
        <?php //if(hontza_crm_is_activado()):?>
        <?php if(hontza_crm_is_show_link_type_action($node)):?>
        <div class="<?php print hontza_crm_link_type_action_class($node);?>">
            <?php print hontza_crm_web_platform_type_link($node);?>
        </div>
        <?php endif;?>
          
          <?php if(red_copiar_is_copiar_activado()):?>
            <?php if(compartir_documentos_custom_access()):?>
            <div class="n-item-copiar-nodo">
                <?php print compartir_documentos_copiar_link($node);?>
            </div>
            <?php endif;?>
         <?php endif;?>
          
          <div class="n-item-editar">
            <?php print hontza_item_edit_link($node);?>
          </div>
          
          <?php //Delete noticias ?>
          <div class="n-item-borrar">
              <?php print hontza_item_delete_link($node);?>
          </div>
          <?php //Fin borrar noticias ?>
          <?php else:?>
            <?php //if(hontza_solr_funciones_is_bookmark_activado()):?>
            <?php if(red_visualizador_is_show_boomark_link()):?>
                <div class="<?php print hontza_solr_funciones_bookmark_action_class($node);?>">
                    <?php print hontza_solr_funciones_bookmark_link($node);?>
                </div>
            <?php endif;?>
          <?php endif;//if usuario_lector?>       
          
          <?php if(hontza_canal_rss_is_visualizador_activado()):?>
            <?php if(publico_is_pantalla_publico('vigilancia')):?>
                <div class="n-item-comentar">
                          <?php print hontza_item_comment_link($node);?>
                </div>
            <?php endif;?>
            <div class="n-item-email">
                <?php print red_send_email($node);?>
            </div>
            <div class="n-item-facebook">
            <?php print red_item_compartir_facebook($node);?>
            </div>
            <div class="n-item-twitter">
                <?php print red_item_compartir_twitter($node);?>
            </div>
            <div class="n-item-linkedin">
                <?php print red_item_compartir_linkedin($node);?>
            </div>
          <?php endif;?>
		
          <div class="items-coments">
                <?php $node_c_d_w=my_get_node_c_d_w($node)?>
                <?php print $node_c_d_w;?>
          </div>
          <?php //intelsat-2015?>
          <?php print hontza_solr_search_fivestar_botonera($node,0,$node_c_d_w);?>
        
    <!--      
    </div>
    -->
	<!--gemini-->	
  </div>
  </div>
  <?php //vista compacta?>
  <?php endif;?>		
  <!--gemini-->  
  <?php endif; ?>
 
  
  <?php if ($page == 1): ?>
    <div id="flagtitulo">
      <div class="f-interesante"><?php print hontza_canal_rss_set_flag_link($node->links['flag-leido_interesante']['title']); ?></div>
      <div class="f-no-interesante"><?php print hontza_canal_rss_set_flag_link($node->links['flag-leido_no_interesante']['title']); ?></div>
      <?php $node_url=hontza_canal_rss_get_node_url($node,1);?>
      <?php $is_send=0;?>
      <?php if(hontza_canal_rss_is_visualizador_activado()):?>
          <?php $is_send=publico_vigilancia_is_send();?>
      <?php endif;?>
      <?php if($is_send):?>
        <div class="f-titulo"><h2<?php print red_copiar_get_title_imported_class($node);?>><?php print l($title,$node_url,array('query'=>  drupal_get_destination(),'html'=>true)); ?></h2></div>
      <?php else:?>
        <div class="f-titulo"><h2<?php print red_copiar_get_title_imported_class($node);?>>
            <?php //intelsat-2016 ?>    
            <?php //print $title ?>
            <?php $node_url=hontza_noticia_usuario_web_link($node,1);?>    
            <?php print l($title,$node_url,array('attributes'=>array('target'=>'_blank'))); ?>    
        </h2></div>
      <?php endif;?>      
  </div>

    <?php //gemini ?>
    <?php //if($node->body): ?>
	<!-- 
	  <div class="noticias-full-texto"><?php //print $node->body; ?></div>
	-->
    <?php //endif; ?>
        <?php if(hontza_node_has_body($node)): ?>      
	  <div class="noticias-full-texto" style="clear:both;">
              <?php print $node->content['body']['#value'];?>              
          </div>        
    <?php endif; ?>
    
	<!--
	<div style="width:100%;">
		<b><?php //print $my_user_info['username'];?></b>
  	</div>
	-->
	
	<div style="clear:both;width:100%;">
        <?php //if(hontza_canal_rss_is_show_user_image()):?>
        <?php if(red_despacho_is_show_user_image()):?> 	    
        <div style="float:left;">
			<!--
			<img title="admin's picture" alt="admin's picture" src="http://92.243.10.49/hontza3/sites/default/files/pictures/picture-1.jpg">
			-->		
			<?php print $my_user_info['img'];?> 
	</div>
        <?php endif;?>    
        <?php if(hontza_canal_rss_is_noticia_usuario_ficha_tabla()):?>
            <?php include('node-noticia_table_view.tpl.php');?>
        <?php else:?>	 
	<div id="i-contenedor">
    <?php if(hontza_canal_rss_is_show_user_image()):?>         
    <div id="ffc">
		<div class="item-fecha">
                    <?php print date('d/m/Y',$node->created); ?>
                </div>
		<div class="item-canal">
                        <span class="etiqueta-gris"><?php print t('Channel');?>: </span> <?php print l(hontza_get_canal_usuarios_title($node->uid),'canal_usuario/'.$node->uid.'/view'); ?>
                </div>
    </div>
    <?php endif;?>    
    		<?php //Bloque de FiveStar ?>
                          <!--  
			  <div class="item-fivestar">
			  	<div style="float:left;">
				<?php //print $node->content['fivestar_widget']['#value'] ?>
				</div>			    
			  	 <div class="item-categorias"<?php //print hontza_item_categorias_style();?>>
                                    <?php //print hontza_todas_etiquetas_html($node);?>
                                </div>  
			  </div>
                          -->                                                    
			  <?php //Fin de bloque de FiveStar  ?>
                          
                          <div class="item-categorias"<?php print hontza_item_categorias_style();?>>
                                    <?php print hontza_todas_etiquetas_html($node);?>
                          </div>
                          <?php if(red_despacho_is_noticia_usuario_source_type_show($node)):?>
                          <div id="ffc">   
                                <div class="field-label-inline-first" style="float:left;"><?php print t('Source Type');?>:</div>
                                <div style="margin-top:0px;float:left;" class="terms terms-inline">
                                    <?php print hontza_solr_funciones_get_item_source_types($node);?>
                                </div>
                          </div>
                          <?php endif;?>
                          <div id="ffc">
                            <?php if(!hontza_canal_rss_is_show_user_image()):?> 
                                    <div class="item-fecha" style="float:left;width: auto;">
                                        <?php print date('d/m/Y',$node->created); ?>
                                    </div>
                                    <?php endif;?>
                                   <?php //intelsat-2015 ?> 
                                    <?php $label_categories=t('Thematic Categories');?>
                                    <?php if(hontza_is_user_anonimo()):?>
                                        <?php $label_categories=t('Categories');?>
                            <?php endif;?>   
                            <div class="field-label-inline-first" style="float:left;"><?php print $label_categories;?>:</div>
                            <div style="margin-top:0px;float:left;" class="terms terms-inline">
                                <?php print hontza_solr_search_get_noticia_categorias_tematicas_html($node,1);?>
                            </div>
                          </div>
   
                          <?php //intelsat-2016?>
                          <!--
                          <?php //if(hontza_crm_is_activado()):?>
                          <div id="ffc" style="padding-left:0px;">
                            <span class="etiqueta-gris"><?php //print t('News link type');?>:</span>
                            <?php //print crm_exportar_get_news_link_type_label($node);?> 
                          </div>
                          <?php //endif;?>
                          
                          <?php if(hontza_is_sareko_id_red()):?>
                                <?php include_once('red-item-fields.tpl.php');?>
                          <?php endif;?>      
                          <?php if(!hontza_is_user_anonimo()):?>
                          <div class="field field-type-text field-item-visitas" style="float:left;clear:both;">
                            <div class="field-items">
                                    <div class="field-item odd">
                                            <div class="field-label-inline-first" style="float:left;">
                                              <?php print t('Visits');?>:&nbsp;
                                            </div>
                                            <?php print red_reads_visitas($node);?>
                                    </div>
                            </div>
                          </div>
                          <?php endif;?>
                          <?php //if(hontza_is_sareko_id_red()):?>
                            <?php print get_reto_al_que_responde_fieldset($node);?>
                          <?php //endif;?>
            
            
    <?php //Obtener el grupo ?>
    <?php $grupo=og_get_group_context()->purl; ?>

  <?php print my_get_node_files($node);?>
  </div>
  <!--table-->
  <?php endif;?>  
  <?php //if(hontza_is_sareko_id_red()):?>
  <!--                        
  <div style="float:left;clear:both;padding-top:10px;">
      <?php //print $links; ?>
  </div>
  -->
  <?php //endif;?>         
             
  <?php if(hontza_is_con_botonera()):?>
  <div class="n-opciones-item">
      <div class="item-fuente">
            <?php print hontza_noticia_usuario_web_link($node);?>
      </div>    
    <?php //intelsat-2015 ?>
    <?php if(!hontza_solr_search_is_usuario_lector()):?>
      <?php //Añadir tag ?>
    <div class="item-etiquetar">
        <?php print hontza_item_tag_link($node);?>
    </div>
    <?php //Fin añadir tag ?>
      
    <?php //Añadir comentario ?>
      <div class="item-comentar">
         <?php print hontza_item_comment_link($node);?> 
      </div>
    <?php //Fin añadir comentario ?>
                
	
    <?php if(red_node_is_show_debate_link($node)):?>  
    <?php //Enviar debate ?>
      <div class="item-debate">
	    <!--
        <a href="<?php //print '/'.$grupo.'/node/add/debate/'.$node->nid?>" title="Enviar a debate" target= "_blank">Enviar a &Aacute;rea de debate</a>
        -->
		<?php //print l(t('Discuss'),'node/add/debate/'.$node->nid,array('attributes'=>array('target'=>'_blank')));?>
                <?php print hontza_node_add_debate_link($node);?>
	  </div>
    <?php //Fin enviar debate ?>
    <?php endif;?>  
    
    <?php if(red_node_is_show_collaboration_link($node)):?>  
    <?php //Enviar trabajo ?>
      <div class="item-trabajo">
	    <!--
        <a href="<?php //print '/'.$grupo.'/node/add/wiki/'.$node->nid ?>" title="Enviar a trabajo"target= "_blank">Enviar a &Aacute;rea de trabajo</a>
        -->
		<?php //print l(t('Collaborate'),'node/add/wiki/'.$node->nid,array('attributes'=>array('target'=>'_blank')));?>
                <?php print hontza_node_add_wiki_link($node);?>
	  </div>
    <?php //Fin enviar trabajo ?>
    <?php endif;?>  
	
    <?php if(red_node_is_show_idea_link($node)):?>   
    <div class="item-crear_idea">
            <?php //print l(t('Idea'),'node/add/idea/'.$node->nid);?>
            <?php print idea_node_add_link($node);?>
    </div>
    <?php endif;?>  
      
          <?php if(is_show_destacar_link()):?>
          <div class="<?php print hontza_noticia_destacar_action_class($node,0);?>">
            <?php //print l(t('Highlight'),'destacar_noticia_usuario/'.$node->nid);?>
            <?php print get_noticia_destacar_link($node);?>
          </div>
          <?php endif;?>
      
          <?php if(boletin_report_is_report_access($node)):?>
            <div class="<?php print boletin_report_action_class($node,0);?>">
                <?php print boletin_report_link($node);?>
            </div>
          <?php endif;?>
      
          <?php //intelsat-2015 ?>
          <?php if(boletin_report_is_save_noticia_boletines_sareko_id()):?>
            <div class="item-noticia-boletines">
                <?php print boletin_report_inc_noticia_usuario_boletines_link($node);?>
            </div>
          <?php endif;?>
          
            <div class="n-item-tipo-fuente">
                <?php print red_despacho_get_reclasificar_tipo_fuente_link($node);?>
            </div>
            <div class="item-categorizar">
                <?php print red_despacho_get_categorizar_link($node);?>
            </div>
      
      
      <?php if(hontza_solr_funciones_is_bookmark_activado()):?>
        <div class="<?php print hontza_solr_funciones_bookmark_action_class($node,0);?>">
            <?php print hontza_solr_funciones_bookmark_link($node);?>
        </div>
      <?php endif;?>
      
      <?php //intelsat-2016?>
        <?php //if(hontza_crm_is_activado()):?>
        <?php if(hontza_crm_is_show_link_type_action($node)):?>
        <div class="<?php print hontza_crm_link_type_action_class($node,1);?>">
            <?php print hontza_crm_web_platform_type_link($node);?>
        </div>
        <?php endif;?>
      
      <?php if(red_copiar_is_copiar_activado()):?>
            <?php if(compartir_documentos_custom_access()):?>
            <div class="n-item-copiar-nodo">
                <?php print compartir_documentos_copiar_link($node);?>
            </div>
            <?php endif;?>
         <?php endif;?>
      
      <div class="item-editar">
        <?php print hontza_item_edit_link($node);?>
      </div>
      
      <?php //Delete noticias ?>
      <div class="item-borrar">
          <?php print hontza_item_delete_link($node);?> 
      </div>
    <?php //Fin borrar noticias ?>
    <?php else:?>
            <?php //if(hontza_solr_funciones_is_bookmark_activado()):?>
            <?php if(red_visualizador_is_show_boomark_link()):?>
                <div class="<?php print hontza_solr_funciones_bookmark_action_class($node);?>">
                    <?php print hontza_solr_funciones_bookmark_link($node);?>
                </div>
            <?php endif;?>  
    <?php endif;//if usuario_lector?>
      
      
      <?php if(hontza_canal_rss_is_visualizador_activado()):?>
            <?php if(publico_is_pantalla_publico('vigilancia')):?>
                <div class="n-item-comentar">
                            <?php print hontza_item_comment_link($node);?>
                </div>
            <?php endif;?>
            <div class="n-item-email">
                <?php print red_send_email($node);?>
            </div>
            <div class="n-item-facebook">
            <?php print red_item_compartir_facebook($node);?>
            </div>
            <div class="n-item-twitter">
                <?php print red_item_compartir_twitter($node);?>
            </div>
            <div class="n-item-linkedin">
                <?php print red_item_compartir_linkedin($node);?>
            </div>
      <?php endif;?>
      
      <div class="items-coments">
          <?php $node_c_d_w=my_get_node_c_d_w($node)?>
          <?php print $node_c_d_w;?>
      </div>
      <?php //intelsat-2015?>
      <?php print hontza_solr_search_fivestar_botonera($node,0,$node_c_d_w);?>
    <?php //endif; ?>
      
  </div>
  <?php endif; ?>          
  <!--</div>--> <!--i-contenedor-->
  </div> <!--clear:both;-->
  </div>
  <?php endif; ?>
<!--
</div>
-->