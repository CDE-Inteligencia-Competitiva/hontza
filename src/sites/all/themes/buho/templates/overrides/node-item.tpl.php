<?php
// $Id: node.tpl.php,v 1.5 2010/06/06 09:51:29  Exp $
?>
<?php //echo print_r($node,1);exit();?>
<?php //print $node->content['socialsharing_bottom']['#value'];?>
<?php //gemini?>
<?php global $base_url;?>
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
      <?php //intelsat-2015 ?>
      <?php $node_url=hontza_canal_rss_get_node_url($node);?>
      <?php //intelsat-2016 ?>
      <?php $title_link_param=red_solr_inc_get_title_link_param();?>
      <?php if(hontza_canal_rss_is_visualizador_activado()):?>
        <?php if(visualizador_is_pantalla('inicio')):?>
      <div class="f-titulo"><h5><b><?php print l(red_solr_inc_resaltar_termino_busqueda(htmlkarakter($title),1),$node_url,$title_link_param);?></b></h5></div>
        <?php else:?>
            <div class="f-titulo"><h2><?php print l(red_solr_inc_resaltar_termino_busqueda(htmlkarakter($title),1),$node_url,$title_link_param);?></h2></div>
        <?php endif;?>
      <?php else:?>
      <div class="f-titulo"><h2><?php print l(red_solr_inc_resaltar_termino_busqueda(htmlkarakter($title),1),$node_url,$title_link_param);?></h2></div>
      <?php endif;?>
	  <?php print my_help_primera_noticia($node);?>
  </div>
  
  <!--
  <div style="width:100%;min*-width:75px;">
	<b><?php //print $my_user_info['username'];?></b>
  </div>
  -->
  <?php //intelsat-2015?>
  
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

            <div class="item-teaser-texto">
                <?php print hontza_content_resumen($node);?>
            </div>
        <?php endif; ?>
        
        <?php //if(hontza_is_sareko_id_red()):?>
            <?php //print red_get_summary_view_html($node);?>
        <?php //endif; ?>
  			 <!-- gemini-->
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
                                <?php //echo print_r($node,1);exit();?>
				<div class="item-canal">
                                <?php //intelsat-2015 ?>    
                                <?php if(hontza_canal_rss_is_publico_activado()):?>
                                  <span class="etiqueta-gris"><?php print t('Channel');?>: </span> <?php print publico_get_canal_link($node); ?>  
                                <?php else:?>    
				  <span class="etiqueta-gris"><?php print t('Channel');?>: </span> <?php print $node->field_item_canal_reference[0]['view']; ?>
				<?php endif;?>
                                </div>
                                <?php endif;?>
			 </div>
  			
			   <?php //Bloque de FiveStar ?>
                          <!--  
			  <div class="item-fivestar">
				<div style="float:left;">
                                
				<?php //print traducir_average($node->content['fivestar_widget']['#value']); ?>			    

                                </div>
                          </div>
                          -->
                                
                          
                          
				 <div class="item-categorias"<?php print hontza_item_categorias_style();?>>
                    <?php  if($node->taxonomy): ?>					
					<!--
                    <span class="etiqueta-gris">Etiquetas:</span>
					-->
					  <?php //gemini ?>					  
                      <?php /*foreach($node->taxonomy as $etiqueta):?>
                        <?php print $etiqueta->name; ?>
                      <?php endforeach; */?>
                      <?php print get_resumen_etiqueta($node->taxonomy,0,$node);?>
                    <?php endif; ?>
                </div>
                <?php //intelsat-2015?>
                <?php //if(hontza_solr_is_solr_activado()):?>
                <?php if(hontza_canal_rss_is_show_categorias_tipos_fuentes_item()):?>          
                      <?php if(!hontza_is_user_anonimo()):?>
                      <div id="ffc">   
                         <div class="field-label-inline-first" style="float:left;"><?php print t('Source Type');?>:</div>
                         <div style="margin-top:0px;float:left;" class="terms terms-inline">
                             <?php print hontza_solr_funciones_get_item_source_types($node);?>
                         </div>
                      </div>      
                      <?php  endif;?>
                      <?php //intelsat-2015 ?>
                      <?php if(!hontza_is_user_anonimo()):?>    
                      <div id="ffc">
                         <?php if(!hontza_canal_rss_is_show_user_image()):?>
                          <?php //if(!hontza_solr_search_is_usuario_lector()):?> 
                                <?php print hontza_canal_rss_get_username_item($my_user_info);?>
                                    <div class="item-fecha" style="float:left;width: auto;">
                                        <?php print date('d/m/Y',$node->created); ?>
                                    </div>                                    
                         <?php //endif;?>
                         <?php endif;?>  
                         <?php //intelsat-2015 ?>                          
                         <?php $label_categories=t('Thematic Categories');?>
                         <?php if(hontza_is_user_anonimo()):?>
                             <?php $label_categories=t('Categories');?>
                         <?php endif;?>
                         <div class="field-label-inline-first" style="float:left;"><?php print $label_categories;?>:</div> 
                         <div style="margin-top:0px;float:left;" class="terms terms-inline">
                             <?php print hontza_solr_funciones_get_item_categorias_tematicas($node);?>
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
        <?php print my_get_node_files($node);?>                 
  </div>
			  <?php //Fin de bloque de FiveStar  ?>
      		  <?php //Tags ?>   
  
  <!--
  </div>
               
			
			
   

         
      
  </div>
  -->
  <?php $url = $_GET['q']; $uri = explode("/", $url); ?>
  <?php if($uri[0] == 'canales'): ?>
   
    </div><!-- end i-contenedor-->
    
    <div class="n-opciones-item">		
	  <?php //gemini ?>
	  <?php $my_purl=og_get_group_context()->purl;?>
	  <div class="n-item-fuente">
          	<?php print hontza_item_web_link($node);?>
          </div>
        
        <?php //intelsat-2015 ?>
        <?php if(!hontza_solr_search_is_usuario_lector()):?>
        <div class="n-item-etiquetar">
		<?php print hontza_item_tag_link($node);?>
        </div>    
        
	  
        <div class="n-item-comentar">
		  <?php print hontza_item_comment_link($node);?>
        </div>     
      
      
          
        <?php if(red_node_is_show_debate_link($node)):?>
        <div class="n-item-debate">
		   <?php print hontza_node_add_debate_link($node);?>
		</div>
        <?php endif;?>
  
      
        <?php if(red_node_is_show_collaboration_link($node)):?>
        <div class="n-item-trabajo">
		 
                 <?php print hontza_node_add_wiki_link($node);?> 
		</div>
        <?php endif;?>
        
        <?php if(red_node_is_show_idea_link($node)):?>
        <div class="n-item-crear_idea">
            
            <?php print idea_node_add_link($node);?>
        </div>
        <?php endif;?>

        <?php if(is_show_destacar_link()):?>
        <div class="<?php print hontza_destacar_action_class($node);?>" id="id_destacar_div_<?php print $node->nid;?>">
            
            <?php print get_destacar_link($node);?>
        </div>
        <?php endif;?>
        
        <?php if(boletin_report_is_report_access($node)):?>
        <div class="<?php print boletin_report_action_class($node);?>">
            <?php print boletin_report_link($node);?>
        </div>
        <?php endif;?>
        
        <?php if(boletin_report_is_save_noticia_boletines_sareko_id()):?>
            <div class="n-item-noticia-boletines">
                <?php print boletin_report_noticia_boletines_link($node);?>
            </div>
         <?php endif;?>
        
        <?php if(red_despacho_is_show_categorizar_link()):?>
            <div class="n-item-tipo-fuente">
                <?php print red_despacho_get_reclasificar_tipo_fuente_link($node);?>
            </div>
            <div class="n-item-categorizar">
                <?php print red_despacho_get_categorizar_link($node);?>
            </div>
        <?php endif;?>
        
        <?php if(hontza_solr_funciones_is_bookmark_activado()):?>
        <div class="<?php print hontza_solr_funciones_bookmark_action_class($node);?>">
            <?php print hontza_solr_funciones_bookmark_link($node);?>
        </div>
        <?php endif;?>
        
        <?php //intelsat-2016?>
        <?php //if(hontza_crm_is_activado()):?>
        <?php if(hontza_crm_is_show_link_type_action()):?>
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
        
          <?php //Delete item ?>
        <div class="n-item-borrar">
            <?php print hontza_item_delete_link($node);?>
                  	
        </div>
        <?php else:?>
            <?php //if(hontza_solr_funciones_is_bookmark_activado()):?>
            <?php if(red_visualizador_is_show_boomark_link()):?>
            <div class="<?php print hontza_solr_funciones_bookmark_action_class($node);?>">
                <?php print hontza_solr_funciones_bookmark_link($node);?>
            </div>
            <?php endif;?>
        <?php endif;//hontza_solr_search_is_usuario_lector()?>
        
        
        
      <?php //Fin borrar item ?>
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
        
	   <?php //if($node->comment_count): ?>
                           <?php $node_c_d_w=my_get_node_c_d_w($node)?> 
                           <div class="items-coments"<?php print hontza_canal_rss_coments_style($node_c_d_w);?>>
				<?php //if($node->comment_count==1): ?>
				  <!--gemini-->
				  <!--
				  <a href="/node/<?php //print $node->nid ?>#comments" title="Ver comentario"><?php //print t('Hay %num_coment comentario', array('%num_coment'=>$node->comment_count)) ?></a>
				  -->
				  <?php //print l(t($node->comment_count.' comentario'),'node/'.$node->nid,array('fragment'=>'comments'));?>
				<?php //else: ?>
				  <!--gemini-->
				  <!--
				  <a href="/node/<?php //print $node->nid ?>#comments" title="Ver comentarios"><?php //print t('Hay %num_coment comentarios', array('%num_coment'=>$node->comment_count)) ?></a>
				  -->
				  <!--				
				  <a href="node/<?php //print $node->nid ?>#comments" title="Ver comentarios"><?php //print t('Hay %num_coment comentarios', array('%num_coment'=>$node->comment_count)) ?></a>              
				  -->			  
				  <?php //print l(t($node->comment_count.' comentarios'),'node/'.$node->nid,array('fragment'=>'comments'));?>
				<?php //endif; ?>
				
                                <?php print $node_c_d_w;?>  
			  </div>
			<?php //endif; ?>
                        <?php //intelsat-2015?>
                        <?php print hontza_solr_search_fivestar_botonera($node,0,$node_c_d_w);?>
       
    </div>  
        
        <!--    
    </div>
    -->
  <!--gemini-->	
  <!--
  </div>
  </div>
  -->		
  <!--gemini-->	
		
  <?php else: ?>
  
  <?php if(hontza_is_con_botonera()):?>
    <div class="n-opciones-item"> 
      <?php //gemini ?>	     
	  <?php if($uri[0]=='comment'):?>
	  	  <div class="n-item-fuente">
                      <?php print hontza_item_web_link($node);?>
                   </div>     
        
                   <?php //intelsat-2015 ?>
                   <?php if(!hontza_solr_search_is_usuario_lector()):?>
                    <?php //Anadir tag ?>
			<div class="n-item-etiquetar">
                            <?php print hontza_item_tag_link($node);?>
                        </div>
		  <?php //Fin anadir tag ?>
        
	  	  <?php //Anadir comentario ?>
			<div class="n-item-comentar">
                            <?php print hontza_item_comment_link($node);?>
                        </div>     
		  <?php //Fin anadir comentario ?>
	  
		 
                  <?php if(red_node_is_show_debate_link($node)):?>  
		  <?php //Enviar debate ?>
			<div class="n-item-debate">
			  <!-- gemini-->
			  <!--
			  <a href="<?php //print $base_url.'/node/add/debate/'.$node->nid?>" title="Enviar a debate" target= "_blank">Enviar a &Aacute;rea de debate</a>
			  -->
			  <?php //print l(t('Discuss'),'node/add/debate/'.$node->nid,array('attributes'=>array('target'=>'_blank')));?>
                          <?php print hontza_node_add_debate_link($node);?>
			</div>
		  <?php //Fin enviar debate ?>
                  <?php endif;?>
	  
                  <?php if(red_node_is_show_collaboration_link($node)):?>
		  <?php //Enviar trabajo ?>
			<div class="n-item-trabajo">
			  <!-- gemini-->
			  <!--
			  <a href="<?php //print $base_url.'/node/add/wiki/'.$node->nid ?>" title="Enviar a trabajo"target= "_blank">Enviar a &Aacute;rea de trabajo</a>
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

                  <?php if(is_show_destacar_link()):?>
                  <div class="<?php print hontza_destacar_action_class($node);?>" id="id_destacar_div_<?php print $node->nid;?>">
                    <?php //print l(t('Highlight'),'destacar_item/'.$node->nid);?>
                    <?php print get_destacar_link($node);?>
                  </div>
                  <?php endif;?>
        
                  <?php if(boletin_report_is_report_access($node)):?>
                    <div class="<?php print boletin_report_action_class($node);?>">
                        <?php print boletin_report_link($node);?>
                    </div>
                  <?php endif;?>
        
                  <?php if(boletin_report_is_save_noticia_boletines_sareko_id()):?>
                    <div class="n-item-noticia-boletines">
                        <?php print boletin_report_noticia_boletines_link($node);?>
                    </div>
                  <?php endif;?>
        
                  <?php if(red_despacho_is_show_categorizar_link()):?>
                    <div class="n-item-tipo-fuente">
                        <?php print red_despacho_get_reclasificar_tipo_fuente_link($node);?>
                    </div>
                    <div class="n-item-categorizar">
                        <?php print red_despacho_get_categorizar_link($node);?>
                    </div>
                  <?php endif;?>
        
                  <?php if(hontza_solr_funciones_is_bookmark_activado()):?>
                    <div class="<?php print hontza_solr_funciones_bookmark_action_class($node);?>">
                        <?php print hontza_solr_funciones_bookmark_link($node);?>
                    </div>
                  <?php endif;?>
        
                  <?php //intelsat-2016?>
                    <?php //if(hontza_crm_is_activado()):?>
                    <?php if(hontza_crm_is_show_link_type_action()):?>
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
        
        
                  <?php //Delete item ?>
			<div class="n-item-borrar">
                            <?php print hontza_item_delete_link($node);?>
                        </div>
		  <?php //Fin borrar item ?>
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
		   
		   <?php //if($node->comment_count): ?>
			  <?php $node_c_d_w=my_get_node_c_d_w($node)?> 
                           <div class="items-coments"<?php print hontza_canal_rss_coments_style($node_c_d_w);?>>
				<?php //if($node->comment_count==1): ?>
				  <!--gemini-->
				  <!--
				  <a href="/node/<?php //print $node->nid ?>#comments" title="Ver comentario"><?php //print t('Hay %num_coment comentario', array('%num_coment'=>$node->comment_count)) ?></a>
				  -->
				  <?php //print l(t($node->comment_count.' comentario'),'node/'.$node->nid,array('fragment'=>'comments'));?>
				<?php //else: ?>
				  <!--gemini-->
				  <!--
				  <a href="/node/<?php //print $node->nid ?>#comments" title="Ver comentarios"><?php //print t('Hay %num_coment comentarios', array('%num_coment'=>$node->comment_count)) ?></a>
				  -->
				  <!--				
				  <a href="node/<?php //print $node->nid ?>#comments" title="Ver comentarios"><?php //print t('Hay %num_coment comentarios', array('%num_coment'=>$node->comment_count)) ?></a>              
				  -->			  
				  <?php //print l(t($node->comment_count.' comentarios'),'node/'.$node->nid,array('fragment'=>'comments'));?>
				<?php //endif; ?>
				<?php //$node_c_d_w=my_get_node_c_d_w($node)?>
                                <?php print $node_c_d_w;?>  
			  </div>
			<?php //endif; ?>
                        <?php //intelsat-2015?>
                        <?php print hontza_solr_search_fivestar_botonera($node,0,$node_c_d_w);?>
	  <?php else:?>	
           
		  <?php //gemini OHARRA::::modu originala else adarrekoa da?>
		  <div class="n-item-fuente">
                      <?php print hontza_item_web_link($node);?>
                  </div>
                  <?php //intelsat-2015 ?>
                  <?php if(!hontza_solr_search_is_usuario_lector()):?>
                  <div class="n-item-etiquetar">
                      <?php print hontza_item_tag_link($node);?>
                  </div>
		  
		  <div class="n-item-comentar">
                      <?php print hontza_item_comment_link($node);?>
                  </div>     
		  	  	  		
                  <?php if(red_node_is_show_debate_link($node)):?>
		  <?php //Enviar debate ?>
			<div class="n-item-debate">
			 <!-- gemini -->
			 <!--	
			  <a href="<?php //print 'node/add/debate/'.$node->nid?>" title="Enviar a debate" target= 

"_blank">Enviar a &Aacute;rea de debate</a>
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
			  <a href="<?php //print 'node/add/wiki/'.$node->nid ?>" title="Enviar a trabajo"target= 

"_blank">Enviar a &Aacute;rea de trabajo</a>
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

                        <?php if(is_show_destacar_link()):?>
                        <div class="<?php print hontza_destacar_action_class($node);?>" id="id_destacar_div_<?php print $node->nid;?>">
                            <?php //print l(t('Highlight'),'destacar_item/'.$node->nid);?>
                            <?php print get_destacar_link($node);?>
                        </div>
                        <?php endif;?>
        
                        <?php if(boletin_report_is_report_access($node)):?>
                            <div class="<?php print boletin_report_action_class($node);?>">
                                <?php print boletin_report_link($node);?>
                            </div>
                        <?php endif;?>
        
                        <?php if(boletin_report_is_save_noticia_boletines_sareko_id()):?>
                            <div class="n-item-noticia-boletines">
                                <?php print boletin_report_noticia_boletines_link($node);?>
                            </div>
                        <?php endif;?>
        
                        <?php if(red_despacho_is_show_categorizar_link()):?>
                            <div class="n-item-tipo-fuente">
                                <?php print red_despacho_get_reclasificar_tipo_fuente_link($node);?>
                            </div>
                            <div class="n-item-categorizar">
                                <?php print red_despacho_get_categorizar_link($node);?>
                            </div>
                        <?php endif;?>
        
                        <?php if(hontza_solr_funciones_is_bookmark_activado()):?>
                            <div class="<?php print hontza_solr_funciones_bookmark_action_class($node);?>">
                                <?php print hontza_solr_funciones_bookmark_link($node);?>
                            </div>
                        <?php endif;?>
        
                        <?php //intelsat-2016?>
                        <?php //if(hontza_crm_is_activado()):?>
                        <?php if(hontza_crm_is_show_link_type_action()):?>
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
        
                        <?php //Delete item ?>
			<div class="n-item-borrar">
                            <?php print hontza_item_delete_link($node);?>
                        </div>
        
          
                        <?php //Fin borrar item ?>
                        <?php else:?>
                            <?php //if(hontza_solr_funciones_is_bookmark_activado()):?>
                            <?php if(red_visualizador_is_show_boomark_link()):?>
                            <div class="<?php print hontza_solr_funciones_bookmark_action_class($node);?>">
                                <?php print hontza_solr_funciones_bookmark_link($node);?>
                            </div>
                            <?php endif;?>
                        <?php endif;//if usuario_lector?>
                        <?php //Bloque de FiveStar ?>
                              
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
        
        
 			 <?php //if($node->comment_count): ?>
                         <?php $node_c_d_w=my_get_node_c_d_w($node)?> 
                          <div class="items-coments"<?php print hontza_canal_rss_coments_style($node_c_d_w);?>>
			  	<?php //if($node->comment_count==1): ?>
				  <!--gemini-->
				  <!--
				  <a href="/node/<?php //print $node->nid ?>#comments" title="Ver comentario"><?php //print t('Hay %num_coment comentario', array('%num_coment'=>$node->comment_count)) ?></a>
				  -->
				  <?php //print l(t($node->comment_count.' comentario'),'node/'.$node->nid,array('fragment'=>'comments'));?>
				<?php //else: ?>
				  <!--gemini-->
				  <!--
				  <a href="/node/<?php //print $node->nid ?>#comments" title="Ver comentarios"><?php //print t('Hay %num_coment comentarios', array('%num_coment'=>$node->comment_count)) ?></a>
				  -->
				  <!--				
				  <a href="node/<?php //print $node->nid ?>#comments" title="Ver comentarios"><?php //print t('Hay %num_coment comentarios', array('%num_coment'=>$node->comment_count)) ?></a>              
				  -->			  
				  <?php //print l(t($node->comment_count.' comentarios'),'node/'.$node->nid,array('fragment'=>'comments'));?>
				<?php //endif; ?>
			    <?php //$node_c_d_w=my_get_node_c_d_w($node)?>
                            <?php print $node_c_d_w;?>      
			  </div>
			<?php //endif; ?>
                        <?php //intelsat-2015?>
                        <?php print hontza_solr_search_fivestar_botonera($node,0,$node_c_d_w);?>
                        
	   <?php //gemini ?>	   
	   <?php endif;?>	  
    <!--
    </div>
    -->
	 <!--gemini-->	
  </div>
  </div>
  <?php endif;?><!--hontza_is_con_botonera-->
  <!--gemini-->
  <?php endif; ?>
  		  
  <?php endif; ?>
  <?php endif; ?>
  <?php if ($page == 1): ?>
  <?php
    if(!repase_organic_group_access()){
        drupal_access_denied();
        exit();
    }
  ?>
  <?php //my_get_tipo_informacion($node->nid);?>  
   <div id="flagtitulo">
      <div class="f-interesante"><?php print hontza_canal_rss_set_flag_link($node->links['flag-leido_interesante']['title']); ?></div>
      <div class="f-no-interesante"><?php print hontza_canal_rss_set_flag_link($node->links['flag-leido_no_interesante']['title']); ?></div>
      
      
      <?php //intelsat-2015 ?>
      <?php $node_url=hontza_canal_rss_get_node_url($node,1);?>
      <?php $is_send=0;?>
      <?php if(hontza_canal_rss_is_visualizador_activado()):?>
          <?php $is_send=publico_vigilancia_is_send();?>
      <?php endif;?>
      <?php if($is_send):?>
        <div class="f-titulo"><h2><?php print l($title,$node_url,array('query'=>  drupal_get_destination(),'html'=>true)); ?></h2></div>
      <?php else:?>
        <div class="f-titulo"><h2>
            <?php //intelsat-2016?>
            <?php //print red_solr_inc_resaltar_termino_busqueda($title,1); ?>
            <?php $node_url=hontza_item_web_link($node,1);?>
            <?php print l($title,$node_url,array('attributes'=>array('target'=>'_blank'))); ?>    
        </h2></div>
      <?php endif;?>
  </div>
      
  
  <?php //if(red_despacho_is_activado()):?>
  <!--
  <div class="field field-type-text field-item-edit-url" style="clear:both;">
                            <div class="field-items">
                                    <div class="field-item odd">
                                            <div class="field-label-inline-first" style="float:left;">
                                              <?php //print t('Url');?>:&nbsp;
                                            </div>
                                            <?php //print red_despacho_get_item_url_enlace_view($node);?>
                                    </div>
                            </div>
  </div>
  -->
  <?php //endif;?>		   	   
	<?php //gemini?>   
    <?php //if($node->body): ?>
      <!--
	  <div class="item-full-texto"><?php //print $node->body; ?></div>
      -->
	<?php //endif; ?>
        <?php if($node->body): ?>
	  <div class="item-full-texto">
              <?php //print $node->content['body']['#value'] ?>
              <?php print hontza_content_full_text($node);?>              
          </div>
	<?php endif; ?>
	
        
      
	<!--
	<div style="width:100%;min-width:75px;">
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
	<div id="i-contenedor">
	
			 <div id="ffc">
                                <?php if(hontza_canal_rss_is_show_user_image()):?> 
			 	<div class="item-fecha">
                                    <?php print date('d/m/Y',$node->created); ?>
                                </div>                                                       
				<div class="item-canal">
				  <span class="etiqueta-gris"><?php print t('Channel');?>: </span> <?php print $node->field_item_canal_reference[0]['view']; ?>
				</div>
                                <?php endif;?>
			 
	
	
    
    
  
  			 <!--    
			  <div class="item-fivestar">
			     <div style="float:left;">
				 <?php //print traducir_average($node->content['fivestar_widget']['#value']); ?>
				 </div>
                          </div>
                          -->  
            <!--
                          <div id="ffc">
                            <span class="etiqueta-gris"><?php //print t('Reads');?>: </span>

                             <?php //print '9';?>
                           </div>
                           -->
                           <div id="ffc" style="padding-left:0px;">
                                <div class="item-categorias"<?php print hontza_item_categorias_style();?>>
                                    <?php print hontza_todas_etiquetas_html($node);?>
                                </div>
                           </div>     
			  
			  
                                                                         
                           <?php //if(hontza_solr_is_solr_activado()):?>
                           <?php if(hontza_canal_rss_is_show_categorias_tipos_fuentes_item()):?>
                                <?php if(!hontza_is_user_anonimo()):?>
                                <div id="ffc" style="padding-left:0px;">
                                   <div class="field-label-inline-first" style="float:left;"><?php print t('Source Type');?>:</div>
                                   <div class="terms terms-inline">
                                       <?php print hontza_solr_funciones_get_item_source_types($node,1);?>
                                   </div>
                                </div>
                                <?php endif;?>    
                                <div id="ffc" style="padding-left:0px;">
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
                                       <?php print hontza_solr_funciones_get_item_categorias_tematicas($node,1);?>
                                   </div>
                                </div>   
                            <?php endif;?>
                           
                            <?php //intelsat-2016?>
                           <!--
                            <?php //if(hontza_crm_is_activado()):?>
                            <div id="ffc" style="padding-left:0px;">
                                <span class="etiqueta-gris"><?php //print t('News link type');?>:</span>
                                <?php //print crm_exportar_get_news_link_type_label($node);?> 
                            </div>
                            -->
                            <?php //endif;?>
                           
      
                          <?php //if(hontza_is_sareko_id_red()):?>
                                <?php //include_once('red-item-fields.tpl.php');?>
                          <?php //endif;?>
                           
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
                           
                           
                            <?php if(hontza_social_is_activado()):?>
                                <?php include_once('social-learning-item-fields.tpl.php');?>
                            <?php endif;?>
                           
                          </div>
                          <?php print my_get_node_files($node);?>
                          </div><!--ffc-->

    <?php //Obtener el grupo ?>
    <?php //$grupo=og_get_group_context()->purl; ?>
                          
  <?php //if(hontza_is_sareko_id_red()):?>
  <!--                        
  <div style="float:left;clear:both;padding-top:10px;">
      <?php //print $links; ?>
  </div>
  -->
  <?php //endif;?>                         
                          
  <?php if(hontza_is_con_botonera()):?>
  <!--                        
  <div class="opciones-item">
  -->
  <div class="n-opciones-item">
    <div class="n-item-fuente">
      <?php print hontza_item_web_link($node);?>
    </div>
      
     <?php //intelsat-2015 ?>
     <?php if(!hontza_solr_search_is_usuario_lector()):?>
      <div class="n-item-etiquetar">
	 <?php print hontza_item_tag_link($node);?>
      </div>
    
    
    
      <div class="n-item-comentar">
	  <?php print hontza_item_comment_link($node);?>	
      </div>
    
    
    
    <?php if(red_node_is_show_debate_link($node)):?>           
    <?php //Enviar debate ?>
      <div class="n-item-debate">
	  	<!--gemini-->
	    <?php //print l(t('Discuss'),'node/add/debate/'.$node->nid,array('attributes'=>array('target'=>'_blank')));?>
            <?php print hontza_node_add_debate_link($node);?>    
	  </div>
    <?php //Fin enviar debate ?>
    <?php endif;?>
      
    <?php $arguments = explode('/', $_GET['q']); //Enviar trabajo  ?>
      <?php if(red_node_is_show_collaboration_link($node)):?>
      <div class="n-item-trabajo">
	    <!--gemini-->
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

      <?php if(is_show_destacar_link()):?>
      <?php //$is_destacar_n=0;?>
      <?php $is_destacar_n=1;?>
      <div class="<?php print hontza_destacar_action_class($node,$is_destacar_n);?>" id="id_destacar_div_<?php print $node->nid;?>">
            <?php //print l(t('Highlight'),'destacar_item/'.$node->nid);?>
            <?php print get_destacar_link($node);?>
      </div>
      <?php endif;?>
      <?php //$is_report_n=0;?>
      <?php $is_report_n=1;?>
      <?php if(boletin_report_is_report_access($node)):?>
        <div class="<?php print boletin_report_action_class($node,$is_report_n);?>">
            <?php print boletin_report_link($node);?>
        </div>
        <?php endif;?>
      
      <?php if(boletin_report_is_save_noticia_boletines_sareko_id()):?>
      <div class="n-item-noticia-boletines">
          <?php print boletin_report_noticia_boletines_link($node);?>
      </div>
      <?php endif;?>
      
        <?php if(red_despacho_is_show_categorizar_link()):?>
            <div class="n-item-tipo-fuente">
                <?php print red_despacho_get_reclasificar_tipo_fuente_link($node);?>
            </div>
            <div class="n-item-categorizar">
                <?php print red_despacho_get_categorizar_link($node);?>
            </div>
        <?php endif;?>
            
        <?php if(hontza_solr_funciones_is_bookmark_activado()):?>
        <div class="<?php print hontza_solr_funciones_bookmark_action_class($node);?>">
            <?php print hontza_solr_funciones_bookmark_link($node);?>
        </div>
        <?php endif;?>
      
        <?php //intelsat-2016?>
        <?php //if(hontza_crm_is_activado()):?>
        <?php if(hontza_crm_is_show_link_type_action()):?>
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
      
        <?php //Delete item ?>
        <div class="n-item-borrar">
	  	<?php print hontza_item_delete_link($node);?>
        </div>
        <?php else:?>    
            <?php //if(hontza_solr_funciones_is_bookmark_activado()):?>
            <?php if(red_visualizador_is_show_boomark_link()):?>
                <div class="<?php print hontza_solr_funciones_bookmark_action_class($node);?>">
                    <?php print hontza_solr_funciones_bookmark_link($node);?>
                </div>
            <?php endif;?>
        <?php endif;//if usuario_lector?>                
	
        <?php if(hontza_canal_rss_is_visualizador_activado()):?>
        <?php if(!publico_vigilancia_is_send()):?>
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
        <?php endif;?>
      
	<?php //if($node->comment_count): ?>
      <!--gemini-2014
      <div class="items-coments-page0">
      -->
      <?php $node_c_d_w=my_get_node_c_d_w($node)?> 
      <div class="items-coments"<?php print hontza_canal_rss_coments_style($node_c_d_w);?>>
        <?php //if($node->comment_count==1): ?>
		  <!--gemini-->
		  <!--
          <a href="/node/<?php //print $node->nid ?>#comments" title="Ver comentario"><?php //print t('Hay %num_coment comentario', array('%num_coment'=>$node->comment_count)) ?></a>
          -->
		  <!--
		  <a href="node/<?php //print $node->nid ?>#comments" title="Ver comentario"><?php //print t('Hay %num_coment comentario', array('%num_coment'=>$node->comment_count)) ?></a>          	
		  -->
		  <?php //print l(t('!num_coment comment', array('!num_coment'=>$node->comment_count)),'node/'.$node->nid,array('fragment'=>'comments','attributes'=>array('class'=>'a_urdina')));?>
		<?php //else: ?>
          <!--gemini-->
		  <!--          
		  <a href="/node/<?php //print $node->nid ?>#comments" title="Ver comentarios"><?php //print t('Hay %num_coment comentarios', array('%num_coment'=>$node->comment_count)) ?></a>
          -->
		  <!--
		  <a href="node/<?php //print $node->nid ?>#comments" title="Ver comentarios"><?php //print t('Hay %num_coment comentarios', array('%num_coment'=>$node->comment_count)) ?></a>          
		  -->
		  <?php //print l(t('!num_coment comments', array('!num_coment'=>$node->comment_count)),'node/'.$node->nid,array('fragment'=>'comments','attributes'=>array('class'=>'a_urdina')));?>
		<?php //endif; ?>
        <?php //$node_c_d_w=my_get_node_c_d_w($node)?>
        <?php print $node_c_d_w;?>          
	  </div>
      <?php //intelsat-2015?>
      <?php print hontza_solr_search_fivestar_botonera($node,0,$node_c_d_w);?>
    <?php //endif; ?>
	
</div> <!--Fin opciones-item--> 
<?php endif;?>
</div> <!--i-contenedor-->
</div> <!--clear:both;-->        
  <?php endif; ?>
<?php if(!hontza_canal_rss_is_publico_activado()):?>
</div>
<?php else:?>
    <?php if(!publico_is_vigilancia_node_view() && !(publico_vigilancia_is_send())):?>
    </div>
    <?php endif;?>
<?php endif;?>