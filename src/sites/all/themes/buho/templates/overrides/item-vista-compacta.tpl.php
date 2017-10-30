<div id="flagtitulo" style="margin-bottom:0px;padding-bottom:10px;">
      <?php if(hontza_solr_funciones_is_bookmark_activado()):?>
        <div style="float:left;">
          <?php print hontza_solr_funciones_get_node_bookmark_checkbox_html($node);?>
        </div>    
      <?php endif;?>
      <div class="f-interesante" style="float:left;"><?php print hontza_canal_rss_set_flag_link($node->links['flag-leido_interesante']['title']); ?></div>
      <div class="f-no-interesante" style="float:left;"><?php print hontza_canal_rss_set_flag_link($node->links['flag-leido_no_interesante']['title']); ?></div>
      <div>
        <div class="n-item-fuente">
        <?php if($node->type=='item'):?>
              <?php print hontza_item_web_link($node,0,1);?>
        <?php else:?>
              <?php print hontza_noticia_usuario_web_link($node,0,1);?>
        <?php endif;?>      
        </div>
      </div>
      <div class="f-titulo"><div  style="font-size:12px;float:left;"><?php print hontza_get_item_fecha_created($node);?>&nbsp;</div><div  style="font-size:12px;font-weight:normal;float:left;"><?php print l(red_funciones_cortar_node_title(htmlkarakter($title)),'node/'.$node->nid,array('attributes'=>array('title'=>red_funciones_get_node_vista_compacta_description($node)),'query'=>  drupal_get_destination()));?></div></div>
	  <?php print my_help_primera_noticia($node);?>
</div>