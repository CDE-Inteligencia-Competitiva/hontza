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
              <?php print social_learning_files_get_collection_file_resumen($node); ?>
          </div>
          <div class="div_idea_list_personas">
                <label><b><?php print t('Filename');?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php print social_learning_files_get_file_name($node);?></div>
          </div>
          <div class="div_idea_list_personas">
                <label><b><?php print t('Server Filename');?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php print social_learning_files_get_server_file_name($node);?></div>
          </div>
          <div class="div_idea_list_personas">
                <label><b><?php print t('File id');?>:</b>&nbsp;</label>
                <div class="item-teaser-texto"><?php print social_learning_files_get_collection_file_node_id($node);?></div>
          </div>
          <?php print my_get_node_files($node);?>
 </div> <!-- end i-contenedor -->         
    <div class="n-opciones-item">		      
      <div class="n-item-editar">
           <?php print social_learning_files_collection_file_edit_link($node);?>
      </div>             
      <div class="n-item-borrar">
           <?php print social_learning_files_collection_file_delete_link($node);?>                  	
      </div>  
      <div class="n-item-upload-rating">
          <?php print social_learning_files_collection_file_upload_link($node);?>
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
                  <div class="field field-type-text field-field-collection-file-filename" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Filename');?>:&nbsp;
					</div>
					<?php print social_learning_files_get_file_name($node);?>
				</div>
			</div>
		  </div>
                  <div class="field field-type-text field-field-collection-file-server-filename" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Server Filename');?>:&nbsp;
					</div>
					<?php print social_learning_files_get_server_file_name($node);?>
				</div>
			</div>
		  </div>
                  <div class="field field-type-text field-field-collection-file-server-file-id" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('File id');?>:&nbsp;
					</div>
					<?php print social_learning_files_get_collection_file_node_id($node);?>
				</div>
			</div>
		  </div>  
                  <div class="field field-type-text field-field-collection-file-ficheros_adjuntos" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Attachments');?>:&nbsp;
					</div>
					<?php print my_get_node_files($node);?>
				</div>
			</div>
		  </div>                    
  </div>
  <?php if(hontza_is_con_botonera()):?>
    <div class="n-opciones-item">		      
      <div class="n-item-editar">
           <?php print social_learning_files_collection_file_edit_link($node);?>
      </div>             
      <div class="n-item-borrar">
           <?php print social_learning_files_collection_file_delete_link($node);?>                  	
      </div>  
      <div class="n-item-upload-rating">
          <?php print social_learning_files_collection_file_upload_link($node);?>
      </div>        
    </div> 
  <?php endif;?>    
</div>  	
<?php endif;?>
</div>