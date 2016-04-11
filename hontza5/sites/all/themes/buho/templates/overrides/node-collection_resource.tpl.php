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
              <?php print social_learning_collections_get_documento_semilla_resumen($node); ?>
          </div>
          
          
          <div class="div_idea_list_personas">
                <label><b><?php print t('Resource id');?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php print social_learning_collections_get_collection_resource_node_id($node);?></div>
          </div>
          
          <div class="div_idea_list_personas">
                <label><b><?php print 'Last upload date';?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php print social_learning_collections_get_documento_semilla_last_upload_date($node);?></div>                    
          </div>
          <!--
          <div class="div_idea_list_personas">
                <label><b><?php //print t('Server Status');?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php //print social_learning_items_get_documento_semilla_server_status_label($node); ?></div>
          </div> 
          -->                     
          <?php if(hontza_is_fivestar_enabled($node)):?>
          <div class="item-fivestar">
                <div style="float:left;">
                    <?php print traducir_average($node->content['fivestar_widget']['#value']); ?>
                </div>
	  </div>
	  <?php endif; ?>

    
 </div> <!-- end i-contenedor -->         
    <div class="n-opciones-item">		
      <div class="n-item-fuente">
          <?php print social_learning_collections_documento_semilla_web_link($node);?>
      </div>
      <div class="n-item-editar">
           <?php print social_learning_collections_documento_semilla_edit_link($node);?>
      </div>             
      <div class="n-item-borrar">
           <?php print social_learning_collections_documento_semilla_delete_link($node);?>                  	
      </div>  
      <div class="n-item-upload-rating">
          <?php print social_learning_collections_documento_semilla_upload_link($node);?>
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
                  <div class="field field-type-text field-field-collection_item-resource-id" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Resource id');?>:&nbsp;
					</div>									
					<?php print social_learning_collections_get_collection_resource_node_id($node);?>  
				</div>
			</div>
		  </div>
                  <div class="field field-type-text field-field-collection-item-last-upload-date" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print 'Last upload date';?>:&nbsp;
					</div>									
					<?php print social_learning_collections_get_documento_semilla_last_upload_date($node);?>  
				</div>
			</div>
		  </div>
                  <!--
                  <div class="field field-type-text field-field-collection-item-server-status" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php //print t('Server Status')?>:&nbsp;
					</div>									
					<?php //print social_learning_items_get_documento_semilla_server_status_label($node);?>  
				</div>
			</div>
		  </div>
                  -->  
	 <?php if(hontza_is_fivestar_enabled($node)):?>  
        <div class="item-fivestar">
                <div style="float:left;">
                    <?php print traducir_average($node->content['fivestar_widget']['#value']); ?>
                </div>
	</div>
        <?php endif;?>  
            
  </div>
  <?php if(hontza_is_con_botonera()):?>
    <div class="n-opciones-item">		
      <div class="n-item-fuente">
          <?php print social_learning_collections_documento_semilla_web_link($node);?>
      </div>
      <div class="n-item-editar">
           <?php print social_learning_collections_documento_semilla_edit_link($node);?>
      </div>             
      <div class="n-item-borrar">
           <?php print social_learning_collections_documento_semilla_delete_link($node);?>                  	
      </div>  
      <div class="n-item-upload-rating">
          <?php print social_learning_collections_documento_semilla_upload_link($node);?>
      </div>        
   </div>  
  <?php endif;?>    
</div>  	
<?php endif;?>
</div>