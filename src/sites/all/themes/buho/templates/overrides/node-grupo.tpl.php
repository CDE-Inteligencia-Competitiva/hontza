<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?> clear-block">
   
  <div class="content">
    <?php $my_user_info=my_get_user_info($node);?>
 
	  <div style="float:left;clear:both;">
			<?php print $my_user_info['img'];?> 
	  </div>
          <?php if(hontza_grupos_mi_grupo_is_ficha_tabla()):?>
          <?php include('node-grupo-table-view.tpl.php');?>
          <?php else:?>	
          <?php if(!red_is_show_source_title()):?>
          <div class="field field-type-text field-field-grupo-creator" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('User');?>:&nbsp;
				</div>
				<?php print $my_user_info['username'];?> 
			</div>
		</div>
	  </div>
          <?php endif?>
	
	  <div class="field field-type-text field-field-grupo-created" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Creation Date');?>:&nbsp;
				</div>
				<?php print date('d-m-Y H:i',$node->created); ?>
			</div>
		</div>
	  </div>	
  
  	  
	  <?php //$my_node=node_load($node->nid)?>
      
          <?php //echo print_r($my_node,1);exit();?>
      
          <?php //echo print_r($node,1);exit();?>
	  
	  <div class="field field-type-text field-field-grupo-resumen" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Summary');?>:&nbsp;
				</div>
                                <!--
                                <div class="my_div_body">
                                -->
					<?php print $node->og_description;?>  
				<!--
                                </div>
                                -->
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-grupo-subject-area" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Subject Area');?>:&nbsp;
				</div>
				<?php print $node->field_tematica[0]['value']; ?>
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-grupo-type-of-group" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Type of group');?>:&nbsp;
				</div>
				<?php print hontza_grupos_mi_grupo_get_type_of_group_string($node); ?>
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-grupo-group-language" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Group language');?>:&nbsp;
				</div>
				<?php print hontza_grupos_mi_grupo_get_group_language_string($node); ?>
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-grupo-creator" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Creator');?>:&nbsp;
				</div>
				<?php print hontza_get_username($node->uid); ?>
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-grupo-chief-editor" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Editor in chief');?>:&nbsp;
				</div>
				<?php print hontza_grupos_get_chief_editor_username($node); ?>
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-grupo-administrators" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Administrators');?>:&nbsp;
				</div>
				<?php print hontza_grupos_get_administrators_html($node); ?>
			</div>
		</div>
	  </div>
      
          <?php if(red_funciones_is_administrador_grupo()):?>
      
          <div class="field field-type-text field-field-grupo-alchemy-key" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Alchemy key');?>:&nbsp;
				</div>
				<?php print $node->field_alchemy_key[0]['value']; ?>
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-grupo-opencalais-key" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Opencalais key');?>:&nbsp;
				</div>
				<?php print $node->field_opencalais_key[0]['value']; ?>
			</div>
		</div>
	  </div>
      
          <?php endif;?>
      
          <div class="field field-type-text field-field-grupo-active-tabs" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Active tabs');?>:&nbsp;
				</div>
				<?php print hontza_grupos_get_active_tabs_html($node); ?>
			</div>
		</div>
	  </div>
            
        <div class="field field-type-text field-field-grupo-delete-rejected-news-time" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Delete rejected news after');?>:&nbsp;
				</div>
				<?php print hontza_grupos_mi_grupo_get_field_delete_rejected_news_time_html($node); ?>
			</div>
		</div>
	  </div>
    
          <div class="field field-type-text field-field-delete-unread-news-time" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Delete unread news after');?>:&nbsp;
				</div>
				<?php print hontza_grupos_mi_grupo_get_field_delete_unread_news_time_html($node); ?>
			</div>
		</div>
	  </div>
    
      
          <div class="field field-type-text field-field-grupo-activate-channels" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Active channels');?>:&nbsp;
				</div>
				<?php print hontza_grupos_get_activate_channels_html($node); ?>
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-grupo-activate-network-connected" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Network connected');?>:&nbsp;
				</div>
				<?php print hontza_grupos_get_network_connected_html($node); ?>
			</div>
		</div>
	  </div>
          <!--table-->
          <?php endif;?>
  <!--end content-->
  </div>
  <!--end node -->  
</div>    