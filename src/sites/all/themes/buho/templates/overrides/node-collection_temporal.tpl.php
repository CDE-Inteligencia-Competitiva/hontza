<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">
<?php $my_user_info=my_get_user_info($node);?>
<?php if($page==0):?>
<div id="flagtitulo">
    <div class="f-titulo"><h2><?php print l(htmlkarakter($title),'social_learning/collection_temporal_view/'.$node->resources_id,array('query'=>drupal_get_destination()));?></h2></div>
</div>
<div style="clear:both;width:100%;">
  <div style="float:left;min-width:75px;">
  		<?php print $my_user_info['img'];?> 
  </div>
  
  <div id="i-contenedor">
	  <div><b><?php print date('d/m/Y H:i',$node->created); ?></b></div>
	  <div class="item-teaser-texto">
              <?php print social_learning_collections_get_collection_temporal_resumen($node); ?>
          </div>
           
  </div>
    <div class="n-opciones-item">		
      <div class="n-item-fuente">
          <?php print social_learning_collections_collection_temporal_web_link($node);?>
      </div>           
   </div>          
<?php elseif($page==1):?>
<!--
<div id="flagtitulo">
	<div class="f-titulo"><h2><?php //print l(htmlkarakter($title),'node/'.$node->nid);?></h2></div>
</div>
-->

<?php //gemini?>
	
          <div class="item-full-texto">
              <?php print $node->description; ?>
          </div>
	
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
  </div>
   <div class="n-opciones-item">		
      <div class="n-item-fuente">
          <?php print social_learning_collections_collection_temporal_web_link($node);?>
      </div>           
   </div>     
</div>  	
<?php endif;?>
</div>