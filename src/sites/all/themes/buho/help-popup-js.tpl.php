<?php
  //  Template for help popup window
  //  Parameters include 
  //  $node which is the complete node object including:
  //  $node->height (window height)
  //  $node->width (window width)
?>
      
  <div id="help_popup">
    <div id="title">
      <?php if ($node->title): print '<h2>'. $node->title .'</h2>'; endif; ?>
 	</div>
	<div id="content">
	  <?php print $node->body?>
	</div>
        <div class="closewindow">
	  <!--gemini-->
	  <!--
	  <input class="popup-button jqmClose" type="button" value="Close window" />
	  -->
          <?php //if($node->nid!='save_current_search'):?>
          <?php //if(!in_array($node->nid,array('save_current_search','guardar_resultado_solr','save_current_rss','send_message_popup','red_exportar_rss_enviar_mail'))):?>
          <?php if(red_copiar_is_help_popup_node($node)):?>  
            <?php print hontza_solr_help_close_window_link();?>
            <!-- gemini -->
            <?php if($user->uid==1):?>
                  <?php if(red_node_is_add_edit_help_popup()):?>
                    <?php //print l('Edit','node/'.$node->nid.'/edit',array('query'=>drupal_get_destination()));?>
                    <?php if($node->nid):?>
                            <?php print l(t('Edit'),'node/'.$node->nid.'/edit',array('query'=>drupal_get_destination()));?>
                    <?php else:?>
                            <?php print l(t('Añadir'),'node/add/my-help',array('query'=>drupal_get_destination()));?>
                    <?php endif;?>
                  <?php endif;?>  
            <?php endif;?>
          <?php endif;?>
          <!--
          <input class="popup-button jqmClose" type="button" value="<?php //print t('Close window');?>" style="display:none;"/>
          -->
	</div>
  </div> <!--// Popup ends -->