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
	  <div class="item-teaser-texto">
              <?php print red_movil_get_item_resumen($node);?>
          </div>
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