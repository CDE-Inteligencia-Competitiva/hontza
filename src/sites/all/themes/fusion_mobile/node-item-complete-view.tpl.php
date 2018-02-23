<div id="flagtitulo">
    <?php if(!hontza_solr_search_is_usuario_lector()):?>
        <div class="f-interesante"><?php print hontza_canal_rss_set_flag_link($node->links['flag-leido_interesante']['title']); ?></div>
        <div class="f-no-interesante"><?php print hontza_canal_rss_set_flag_link($node->links['flag-leido_no_interesante']['title']); ?></div>
    <?php endif;?>    
    <div class="f-titulo"><h2><?php print l(htmlkarakter($title),'node/'.$node->nid,array('query'=>  drupal_get_destination()));?></h2></div>
</div>
<div style="clear:both;width:100%;">
  <div class="div_user_img">
  		<!--
		<img title="admin's picture" alt="admin's picture" src="http://92.243.10.49/hontza3/sites/default/files/pictures/picture-1.jpg">
		-->		
		<?php print $my_user_info['img'];?> 
  </div>
  
  <div id="i-contenedor">
          <!--
	  <div class="item-teaser-texto">
              <?php //print red_movil_get_item_resumen($node);?>
          </div>
          -->
          <?php if($node->body): ?>
	  <div class="item-full-texto">
              <?php //print $node->content['body']['#value'] ?>
              <?php print hontza_content_full_text($node);?>              
          </div>
          <?php endif; ?>
          <?php if($page!=0):?>
          <div style="clear:both;"><b><?php print date('d/m/Y',$node->created); ?></b></div>
	  <?php endif;?>
          
    <!--      
    <div id="ffc">   
        <div class="field-label-inline-first" style="float:left;"><?php //print t('Thematic Categories');?>:</div>
            <div style="margin-top:0px;float:left;" class="terms terms-inline">
            <?php //print hontza_solr_funciones_get_item_categorias_tematicas($node);?>
        </div>
    </div>
    -->
    <?php if($page!=0):?>
    <div class="item-canal">
        <span class="etiqueta-gris"><?php print t('Channel');?>: </span> <?php print strip_tags($node->field_item_canal_reference[0]['view']); ?>
    </div>
    <div id="ffc" class="div_movil_fcc">
        <div class="item-categorias"<?php print hontza_item_categorias_style();?>>
        <?php print hontza_todas_etiquetas_html($node);?>
        </div>
    </div>
    <?php //if(hontza_solr_is_solr_activado()):?>
        <?php if(hontza_canal_rss_is_show_categorias_tipos_fuentes_item()):?>
                                <?php if(!hontza_is_user_anonimo()):?>
                                <div id="ffc"  class="div_movil_fcc">
                                   <div class="field-label-inline-first" style="float:left;"><?php print t('Source Type');?>:</div>
                                   <div  class="terms terms-inline div_source_type">
                                       <?php print hontza_solr_funciones_get_item_source_types($node,1);?>
                                   </div>
                                </div>
                                <?php endif;?>    
                                <!--
                                <div id="ffc"  class="div_movil_fcc">
                                -->
                                   <?php //if(!hontza_canal_rss_is_show_user_image()):?> 
                                    <!--
                                    <div class="item-fecha" style="float:left;width: auto;">
                                        <?php //print date('d/m/Y',$node->created); ?>
                                    </div>
                                    -->
                                    <?php //endif;?>
                                <!--    
                                </div>
                                -->
                                <div id="ffc"  class="div_movil_fcc">    
                                                                      <?php //intelsat-2015 ?> 
                                    <?php $label_categories=t('Thematic Categories');?>
                                    <?php if(hontza_is_user_anonimo()):?>
                                        <?php $label_categories=t('Categories');?>
                                    <?php endif;?>

                                   <div class="field-label-inline-first" style="float:left;"><?php print $label_categories;?>:</div>
                                   <div class="terms terms-inline div_categorias_tematicas">
                                       <?php print hontza_solr_funciones_get_item_categorias_tematicas($node,1);?>
                                   </div>
                                </div>   
        <?php endif;?>
        <?php //if(hontza_is_sareko_id_red || red_is_subdominio_proyecto_alerta()):?>
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
        <div id="ffc"  class="div_movil_fcc">
            <?php print my_get_node_files($node);?>
        </div>    
    <?php endif;?>
    <?php if(hontza_is_con_botonera()):?>
        <div class="n-opciones-item">
            <div class="n-item-fuente">
                <?php print red_movil_item_web_link($node);?>
        </div>
        <?php if(!hontza_solr_search_is_usuario_lector()):?>
            <div class="n-item-movil-theme">
               <?php print red_movil_item_tag_link($node);?>
            </div>    
            <div class="n-item-movil-theme">
              <?php print red_movil_item_comment_link($node);?>	
            </div>    
            <?php if(hontza_solr_funciones_is_bookmark_activado()):?>
            <div class="<?php print hontza_solr_funciones_bookmark_action_class($node);?>">
                <?php print hontza_solr_funciones_bookmark_link($node);?>
            </div>
            <?php endif;?>
        <?php endif;?>
        <?php $node_c_d_w='';?>    
        <?php print hontza_solr_search_fivestar_botonera($node,0,$node_c_d_w,0);?>    
    <?php endif;?>        
    </div> <!-- end i-contenedor -->          
</div>