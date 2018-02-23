<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">
<?php $my_user_info=my_get_user_info($node);?>
<?php if($page==0):?>    
<div id="flagtitulo">
    <?php print hontza_get_debate_img();?>&nbsp; 
    <div class="f-titulo"><h2><?php print l(htmlkarakter($title),'node/'.$node->nid,array('query'=>drupal_get_destination()));?></h2></div>
</div>
<div style="clear:both;width:100%;">
  <div style="float:left;min-width:75px;">
  		<!--
		<img title="admin's picture" alt="admin's picture" src="http://92.243.10.49/hontza3/sites/default/files/pictures/picture-1.jpg">
		-->		
		<?php print $my_user_info['img'];?> 
  </div>
  
  <div id="i-contenedor">
	  <div><b><?php print date('d/m/Y H:i',$node->created); ?></b></div>
	  <div class="item-teaser-texto">
              <?php print hontza_debate_resumen($node); ?>
          </div>	  
          <div class="item-categorias"<?php print hontza_item_categorias_style();?>>
                    <?php  if($node->taxonomy): ?>					
					<!--
                    <span class="etiqueta-gris">Etiquetas:</span>
					-->
					  <?php //gemini ?>					  
                      <?php /*foreach($node->taxonomy as $etiqueta):?>
                        <?php print $etiqueta->name; ?>
                      <?php endforeach; */?>
                      <b><?php print get_resumen_etiqueta($node->taxonomy);?></b>
                    <?php endif; ?>
          </div>
          
          <div id="ffc">   
            <div class="field-label-inline-first" style="float:left;"><?php print t('Thematic Categories');?>:</div>
                <div style="margin-top:0px;float:left;" class="terms terms-inline">
                <?php print hontza_solr_funciones_get_item_categorias_tematicas($node);?>
            </div>
          </div>
          
          <!--
          <?php //if(hontza_is_fivestar_enabled($node)):?>
          <div class="item-fivestar">
                <div style="float:left;">
                    <?php //print traducir_average($node->content['fivestar_widget']['#value']); ?>
                </div>
	  </div>
	  <?php //endif; ?>
          -->

    <?php print my_get_node_files($node);?>
 </div> <!-- end i-contenedor -->         
    <div class="n-opciones-item">  
		<!--
                <div class="n-item-fuente">
			<?php //print hontza_debate_origin_link($node);?>
		</div>
                -->
                <?php //intelsat-2015 ?>
		<?php if(!hontza_solr_search_is_usuario_lector()):?>
                <div class="n-item-etiquetar">
			<?php //print l(t('Tag'),'node/'.$node->nid.'/tag',array('query'=>drupal_get_destination()));?>
                        <?php print hontza_debate_tag_link($node);?>
		</div>
        
        
                <div class="n-item-comentar">
			<?php //print l(t('Comment'),'comment/reply/'.$node->nid,array('fragment'=>'comment-form','query'=>drupal_get_destination()));?>
                        <?php //print l(t('Comment'),$_GET['q'],array('fragment'=>'comment-form','query'=>drupal_get_destination()));?>
                        <?php print hontza_debate_comment_link($node);?>
		</div>
		
                <?php if(red_node_is_show_idea_link($node)):?> 
		<div class="n-item-crear_idea">
                    <?php print idea_node_add_link($node);?>  
		</div>
                <?php endif;?>
        
                <?php if(boletin_report_is_report_access($node)):?>
                    <div class="<?php print boletin_report_action_class($node);?>">
                        <?php print boletin_report_link($node);?>
                    </div>
                <?php endif;?>
        
                <?php if(boletin_report_is_save_noticia_boletines_sareko_id()):?>
                    <div class="n-item-noticia-boletines">
                        <?php print boletin_report_debate_boletines_link($node);?>
                    </div>
                <?php endif;?>
        
                <?php //intelsat-2015?>
                <?php if(hontza_canal_rss_is_debate_editar_access($node)):?>
                <div class="n-item-editar">
                    <?php //print l(t('Edit'),"node/".$node->nid.'/edit');?>
                    <?php print hontza_debate_edit_link($node);?>
		</div>
               
        
                <div class="n-item-borrar">
                    <?php print hontza_debate_delete_link($node);?>  
		</div>
                <?php endif;?>
                <?php endif;?>
                <?php //intelsat-2015?>
                <?php $node_c_d_w=my_get_node_c_d_w($node)?> 
                <div class="items-coments"<?php print hontza_canal_rss_coments_style($node_c_d_w);?>>
                    <?php print $node_c_d_w;?>  
                </div>
                <?php //intelsat-2015?>
                <?php print hontza_solr_search_fivestar_botonera($node,1);?>
	</div>
  <!--
    </div>
  -->  
</div>  	
<?php elseif($page==1):?>
<!--
<div id="flagtitulo">
	<div class="f-titulo"><h2><?php //print l(htmlkarakter($title),'node/'.$node->nid);?></h2></div>
</div>
-->

<?php //gemini?>
	
        <?php if(hontza_node_has_body($node)): ?>
	  <div class="item-full-texto">
              <?php print $node->content['body']['#value'] ?>
          </div>
	<?php endif; ?>
<div style="clear:both;width:100%;">
  <div class="contenedor_left">
              <div class="user_img_left">
                    <?php print $my_user_info['img'];?>
              </div>              
  </div>
  
  <div id="i-contenedor">
	<?php //if($node->body): ?>
	  <div><b><?php print date('d/m/Y H:i',$node->created); ?></b></div>
    <?php //endif; ?>
	 <div class="item-categorias"<?php print hontza_item_categorias_style();?>>
                    <?php print hontza_todas_etiquetas_html($node);?>
        </div>
          
          <div id="ffc" style="padding-left:0px;">   
                                   <div class="field-label-inline-first" style="float:left;"><?php print t('Thematic Categories');?>:</div>
                                   <div style="margin-top:0px;float:left;" class="terms terms-inline">
                                       <?php print hontza_solr_funciones_get_item_categorias_tematicas($node,1);?>
                                   </div>
          </div>
          
          <?php print get_reto_al_que_responde_fieldset($node);?>
          
        <!--  
        <?php //if(hontza_is_fivestar_enabled($node)):?>  
        <div class="item-fivestar">
                <div style="float:left;">
                    <?php //print traducir_average($node->content['fivestar_widget']['#value']); ?>
                </div>
	</div>
        <?php //endif;?>
        -->
    <div class="field field-type-text field-field-debate-ficheros_adjuntos" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Attachments');?>:&nbsp;
					</div>
					<?php print my_get_node_files($node);?>
				</div>
			</div>
		  </div>
          
    <div class="field field-type-text field-field-debate-enlaces" style="float:left;clear:both;">  
       <?php $links_content=hontza_get_enlaces_view_html($node,0,1);?>
       <?php if(!empty($links_content)):?> 
        <h3><?php print t('Links')?></h3> 
        <?php print $links_content;?>
       <?php endif;?> 
   </div>        
    </div>      
    <?php if(hontza_is_con_botonera()):?>
    <!--
    <div class="n-opciones-item" style="padding-bottom:10px;">
    -->
    <div class="n-opciones-item">
		<!--
                <div class="n-item-fuente">
			<?php //print hontza_debate_origin_link($node);?>
		</div>
                -->
                
		<?php //intelsat-2015 ?>
		<?php if(!hontza_solr_search_is_usuario_lector()):?>
                <div class="n-item-etiquetar">
			<?php //print l(t('Tag'),'node/'.$node->nid.'/tag',array('query'=>drupal_get_destination()));?>
                        <?php print hontza_debate_tag_link($node);?>
		</div>
        
                <div class="n-item-comentar">
			<?php //print l(t('Comment'),'comment/reply/'.$node->nid,array('fragment'=>'comment-form','query'=>drupal_get_destination()));?>
                        <?php //print l(t('Comment'),$_GET['q'],array('fragment'=>'comment-form','query'=>drupal_get_destination()));?>
                        <?php print hontza_debate_comment_link($node);?>
		</div>
		
                <?php if(red_node_is_show_idea_link($node)):?> 
		<div class="n-item-crear_idea">
                    <?php print idea_node_add_link($node);?>  
		</div>
                <?php endif;?>
        
                <?php if(boletin_report_is_report_access($node)):?>
                    <div class="<?php print boletin_report_action_class($node);?>">
                        <?php print boletin_report_link($node);?>
                    </div>
                <?php endif;?>
        
                <?php if(boletin_report_is_save_noticia_boletines_sareko_id()):?>
                    <div class="n-item-noticia-boletines">
                        <?php print boletin_report_debate_boletines_link($node);?>
                    </div>
                <?php endif;?>
        
                <?php //intelsat-2015?>
                <?php if(hontza_canal_rss_is_debate_editar_access($node)):?>
                <div class="n-item-editar">
                    <?php //print l(t('Edit'),"node/".$node->nid.'/edit');?>
                    <?php print hontza_debate_edit_link($node);?>
		</div>
                
        
                <div class="n-item-borrar">
                    <?php print hontza_debate_delete_link($node);?>  
		</div>
                <?php endif;?>
                <?php endif;?>
                <?php //intelsat-2015?>
                <?php $node_c_d_w=my_get_node_c_d_w($node)?> 
                <div class="items-coments"<?php print hontza_canal_rss_coments_style($node_c_d_w);?>>
                    <?php print $node_c_d_w;?>  
                </div>
                <?php //intelsat-2015?>
                <?php print hontza_solr_search_fivestar_botonera($node,1);?>
  </div>
  <?php endif;?>          
  
  
     
</div>  	
<?php endif;?>
</div>