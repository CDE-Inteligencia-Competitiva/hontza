<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">
<?php $my_user_info=my_get_user_info($node);?>
<?php if($page==0):?>
<div id="flagtitulo">
    <div class="f-titulo"><h2><?php print l(htmlkarakter($title),'node/'.$node->nid,array('query'=>drupal_get_destination()));?></h2></div>
</div>
<div style="clear:both;width:100%;">
  <div style="float:left;min-width:75px;">
  		<?php print $my_user_info['img'];?> 
  </div>
  
  <div id="i-contenedor">
	  <div><b><?php print date('d/m/Y H:i',$node->created); ?></b></div>
	  <div class="item-teaser-texto">
              <?php print social_learning_collections_get_collection_resumen($node); ?>
          </div>
          
          <div class="div_idea_list_personas">
                <label><b><?php print t('Collection id');?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php print social_learning_collections_get_collection_id($node);?></div>
          </div>
          
          <div class="div_idea_list_personas">
                <label><b><?php print 'Last upload date';?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php print social_learning_collections_get_last_upload_date($node);?></div>
          </div>
          
          <div class="div_idea_list_personas">
                <label><b><?php print 'Last resource upload date';?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php print social_learning_collections_get_last_resource_upload_date($node);?></div>
          </div>
          
          <div class="div_idea_list_personas">
                <label><b><?php print 'Last news upload date';?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php print social_learning_items_get_collection_last_news_upload_date($node);?></div>
          </div>
          
          <div class="div_idea_list_personas">
                <label><b><?php print 'Last news download date';?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php print social_learning_collections_get_last_resource_download_date($node);?></div>
          </div>
          <!--
          <?php //$info_results=social_learning_step_get_collection_numero_de_resultados($node);?>
          <div class="div_idea_list_personas">
                <label><b><?php //print 'Number of results';?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php //print $info_results['numero_de_resultados'];?></div>
          </div>
          <div class="div_idea_list_personas">
                <label><b><?php //print 'Unread results';?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php //print $info_results['sin_leer'];?></div>
          </div>
          -->
          
 </div> <!-- end i-contenedor -->
 <div class="n-opciones-item">		
      <div class="n-item-editar">
           <?php print social_learning_collections_collection_edit_link($node);?>
      </div>             
      <div class="n-item-borrar">
           <?php print social_learning_collections_collection_delete_link($node);?>                  	
      </div>
      <div class="n-collection-resources">
          <?php print social_learning_collections_collection_resources_link($node);?>
      </div>
      <div class="n-item-upload-rating">
           <?php print social_learning_collections_collection_upload_link($node);?>                  	
      </div>
      <div class="n-collection-download">
           <?php print social_learning_collections_collection_item_download_link($node);?>                  	
      </div>
      <div class="n-collection-results">
           <?php print social_learning_collections_collection_results_link($node);?>                  	
      </div>
      <div class="n-collection-filtro-rss">
           <?php print social_learning_collections_collection_filtro_rss_link($node);?>                  	
      </div>  
    </div>  
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
                  <div class="field field-type-text field-field-collection-collection-id" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Collection id');?>:&nbsp;
					</div>									
					<?php print social_learning_collections_get_collection_id($node);?>  
				</div>
			</div>
		  </div>
                  <div class="field field-type-text field-field-collection-collection-last-upload-date" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print 'Last upload date';?>:&nbsp;
					</div>									
					<?php print social_learning_collections_get_last_upload_date($node);?>  
				</div>
			</div>
		  </div>
                  <div class="field field-type-text field-field-collection-collection-last-resource-upload-date" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print 'Last resource upload date';?>:&nbsp;
					</div>									
					<?php print social_learning_collections_get_last_resource_upload_date($node);?>  
				</div>
			</div>
		  </div>
                            
                  <div class="field field-type-text field-field-collection-collection-last-resource-download-date" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print 'Last news upload date';?>:&nbsp;
					</div>									
					<?php print social_learning_items_get_collection_last_news_upload_date($node);?>  
				</div>
			</div>
		  </div>
                  
                  <div class="field field-type-text field-field-collection-collection-last-resource-download-date" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print 'Last news download date';?>:&nbsp;
					</div>									
					<?php print social_learning_collections_get_last_resource_download_date($node);?>  
				</div>
			</div>
		  </div>
          
                  <?php $info_results=social_learning_step_get_collection_numero_de_resultados($node);?>                                        
                  <div class="field field-type-text field-field-collection-collection-last-resource-numero_de_resultados" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print 'Number of results';?>:&nbsp;
					</div>									
					<?php print $info_results['numero_de_resultados'];?>
				</div>
			</div>
		  </div>
          
                  <div class="field field-type-text field-field-collection-collection-last-resource-sin_leer" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print 'Unread results';?>:&nbsp;
					</div>									
					<?php print $info_results['sin_leer'];?>
				</div>
			</div>
		  </div>
          
                  <?php if(isset($node->my_analisis) && !empty($node->my_analisis)):?>                          
                    <div class="field field-type-text field-field-collection-collection-resource-download-analisis" style="float:left;clear:both;">
                          <div class="field-items">			
                                  <div class="field-item odd">			
                                          <div class="field-label-inline-first" style="float:left;">
                                            <?php print t('Analysis');?>:&nbsp;
                                          </div>
                                          <?php print $node->my_analisis;?>
                                  </div>
                          </div>
                    </div>	  	  
                  <?php endif;?>
  </div>  
</div>  	
<?php endif;?>
</div>