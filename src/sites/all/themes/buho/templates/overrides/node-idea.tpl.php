<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?><?php print my_class_primera_idea($node,$page);?>">
  <?php //echo print_r($node,1);?>
  <?php $my_user_info=my_get_user_info($node);?>
  <?php if ($page == 0):?>
  <div id="flagtitulo">
      <?php print get_idea_bombilla_img();?>
      <div class="f-titulo"><h2><?php print l(estrategia_set_title_max_len(htmlkarakter($title)),'node/'.$node->nid,array('query'=>drupal_get_destination()));?></h2></div>
	  <?php print my_help_primera_idea($node);?>
  </div>
  <div style="clear:both;width:100%;">
         <div class="contenedor_left">
              <div class="user_img_left">
                    <?php print $my_user_info['img'];?>
              </div>
              <div class="div_suma_votos_parent">
                  <div class="div_suma_votos">
                    <?php print $node->suma_votos;?>
                  </div>
              </div>
          </div>
  	  <div id="i-contenedor">
                <!-- gemini-->
                <div id="ffc">
                    <!--
                    <div>
                        <label><b><?php //print t('Total score')?>:</b></label>
                        <?php //print $node->suma_votos;?>
                    </div>
                    -->

                    <div class="item-fecha">
                        <?php print date('d-m-Y',$node->created);?>
                    </div>

                <?php if(hontza_node_has_body($node)): ?>
                  <div class="item-teaser-texto">
                      <?php print hontza_idea_resumen($node); ?>
                  </div>
                <?php endif; ?>
                  
                  <div class="div_idea_list_personas">
                        <label><b><?php print t('Subgroup');?>:</b>&nbsp;</label>
                        
                        <div class="item-teaser-texto" style="float:none;"><?php print get_idea_subgrupo_string_list($node); ?></div>
                  </div>

                  <div class="div_idea_list_personas">
                        <label><b><?php print t('Guests');?>:</b>&nbsp;</label>

                        <div class="item-teaser-texto"  style="float:none;"><?php print get_idea_invitados_string_list($node); ?></div>
                  </div>

                  <div class="div_idea_list_personas">
                        <label><b><?php print t('Supported by');?>:</b>&nbsp;</label>

                        <div class="item-teaser-texto" style="float:none;" id="id_supported_by_<?php print $node->nid;?>"><?php print get_idea_adheridas_string_list($node); ?></div>
                  </div>

                  <div class="field field-type-text field-field-idea-categorias-tematicas" style="float:left;clear:both;">
                    <div class="field-items">
                            <div class="field-item odd">
                                    <div class="field-label-inline-first" style="float:left;">
                                      <?php print t('Thematic Categories');?>:&nbsp;
                                    </div>
                                      <?php if (count($node->categorias_tematicas)>0): ?>
                                            <div class="terms terms-inline" style="margin-top:0px;"><?php print $node->categorias_tematicas_html ?></div>
                                      <?php else:?>
                                            <div class="terms terms-inline" style="margin-top:0px;"><?php print t('Undefined category'); ?></div>
                                      <?php endif;?>
                            </div>
                    </div>
                  </div>

                  <!--
                  <div class="idea_list_reto">
                        <label><b><?php //print t('Challenge');?>:</b>&nbsp;</label>
                       
                        <div class="item-teaser-texto"><?php //print $node->reto_al_que_responde; ?></div>
                  </div>
                  -->

                  <?php print get_reto_al_que_responde_fieldset($node)?>
                  
				<!--
				<div class="item-canal">
				  <span class="etiqueta-gris"><?php //print t('Persona origen')?>: </span> <?php //print $node->persona_origen_name; ?>
				</div>				
			 </div>
			 -->
  			<?php //print $node->content['fivestar_widget']['#value'];exit(); ?>
			   <?php //Bloque de FiveStar ?>
                         <!--
			  <div class="item-fivestar">
				<div>
                                <?php //if($node->es_mi_idea):?>
                                    <?php //print $node->my_stars;?>                                    
                                <?php //else:?>
                                    
                                    <div style="float:left;">
                                    
                                    <?php //print traducir_average($node->content['fivestar_widget']['#value']); ?>
                                    </div>    
			  	<?php //endif;?>
                                </div>				
			  </div>
                          -->  
                          

                           <div class="div_idea_list_personas">
                                <label><b><?php print t('Tags');?>:</b>&nbsp;</label>

                                <div class="item-teaser-texto"<?php print hontza_item_categorias_style(1);?>>
                                    <?php  if($node->taxonomy): ?>
                                      <?php print get_resumen_etiqueta($node->taxonomy);?>
                                    <?php endif; ?>
                                </div>
                            </div>
                              
			  
                          <?php print my_get_node_files($node);?>
                          </div><!-- end fcc-->
			  </div><!-- end i-contenedor-->
			  <div class="n-opciones-item">
                                        <!--
                                        <div class="n-item-fuente">			
                                            <?php //print idea_origin_link($node);?>        
                                        </div>
                                        -->
	 				<?php //intelsat-2015?>
                                        <?php if(!hontza_solr_search_is_usuario_lector()):?>
                                          <div class="n-item-etiquetar">
						<?php print idea_tag_link($node);?>
					  </div>

                                        <div class="n-item-comentar">
			  			<?php print idea_comment_link($node);?>
					</div>     

                                        
                                        <?php print link_adherirse($node,'n');?>

                                        
                                        <?php print link_add_oportunidad($node,'n');?>
                                        
                                        
                                        <div class="n-item-editar">
					    <?php print idea_edit_link($node);?>
					</div>
                              
					<div class="n-item-borrar">					
					  <?php print idea_delete_link($node);?>
					</div>
                                        <?php endif;?>
                                         
                                          
									
					  <div class="items-coments">						
						<?php $node_c_d_w=my_get_node_c_d_w($node);?>
                                                <?php print $node_c_d_w;?>
					  </div>
                                        
                                          <?php //intelsat-2015?>
                                          <?php print hontza_solr_search_fivestar_botonera($node,1,$node_c_d_w);?>
                            
					  										  
			  <?php //end n-opciones-item?>
			  </div>
	 <?php //end contenedor?>
	 <!--
         </div>
         -->
  </div>	  
  <?php //page==1?>
  <?php else:?>
	
	  <?php //echo print_r($node,1);?>
	 
	  <div class="content">	  	  
		  <?php $my_user_info=my_get_user_info($node);?>
	 
		  <div style="float:left;clear:both;">
				<?php print $my_user_info['img'];?> 
		  </div>
		  <?php if(idea_enlazar_is_ficha_tabla()):?>
                  <?php include('node-idea-table-view.tpl.php');?>
                  <?php else:?>	
		  <!--
		  <div class="field field-type-text field-field-idea-type" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php //print t('Type');?>:&nbsp;
					</div>
					<?php //print $node->oportunidad_type_name ?>        
				</div>
			</div>
	  	  </div>
		  -->

                   <div class="field field-type-text field-field-idea-puntuacion_total" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php print t('Total score');?>:&nbsp;
					</div>
					<?php print $node->suma_votos;?>
				</div>
			</div>
	  	  </div>

		  <div class="field field-type-text field-field-idea-created" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php print t('Creation Date');?>:&nbsp;
					</div>
					<?php print date('d-m-Y H:i',$node->created);?>
				</div>
			</div>
	  	  </div>

                <div class="field field-type-text field-field-idea-categorias-tematicas" style="float:left;clear:both;">
                    <div class="field-items">
                            <div class="field-item odd">
                                    <div class="field-label-inline-first" style="float:left;">
                                      <?php print t('Thematic Categories');?>:&nbsp;
                                    </div>
                                      <?php if (count($node->categorias_tematicas)>0): ?>
                                            <div class="terms terms-inline" style="margin-top:0px;"><?php print $node->categorias_tematicas_html ?></div>
                                      <?php else:?>
                                            <div class="terms terms-inline" style="margin-top:0px;"><?php print t('Undefined category'); ?></div>
                                      <?php endif;?>
                            </div>
                    </div>
                </div>
		
                <div class="field field-type-text field-field-idea-argumentacion" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Full Text');?>:&nbsp;
					</div>									
					<div class="my_div_body">
						<?php print $node->content['body']['#value'];?>  
					</div>
				</div>
			</div>
		  </div>
		  
		  <div class="field field-type-text field-field-idea-evaluacion_idea" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<!--
					<div class="field-label-inline-first" style="float:left;">
					  <?php //print t('Attachments');?>:&nbsp;
					</div>
					-->
                                        <!--
					 <div class="item-fivestar">
						<div>
                                                <?php //if($node->es_mi_idea):?>
                                                
                                                    <?php //print $node->my_stars;?>
                                                <?php //else:?>
                                                <div style="float:left;">
                                                    <?php //print traducir_average($node->content['fivestar_widget']['#value']); ?>
						</div>
                                                <?php //endif;?>
                                                 
                                                </div>

                                        </div>
                                        -->                                        
                                </div>
                        </div>
                  </div>    

                  <div class="field field-type-text field-field-idea-puntuacion_total" style="float:left;clear:both;">
				<div class="field-items">
					<div class="field-item odd">
						<div class="field-label-inline-first" style="float:left;">
						  <?php print t('Total score');?>:&nbsp;
						</div>
						<?php print $node->suma_votos;?>
					</div>
				</div>
		  </div>
		  		 
		  <?php //echo print_r($node,1);?>
			<div class="field field-type-text field-field-idea-categorias_list" style="float:left;clear:both;">
				<div class="field-items">
					<div class="field-item odd">
						<div class="field-label-inline-first" style="float:left;">
						  <?php print t('Tags');?>:&nbsp;
						</div>
						<div style="float:left;">
							 <div class="item-categorias"<?php print hontza_item_categorias_style(1);?>>
								<?php print hontza_todas_etiquetas_html($node);?>
							</div>  
						</div>        
					</div>
				</div>
		  </div>

                  
                  <!--
                   <div class="field field-type-text field-field-idea-reto_al_que_responde" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php //print t('Associated Challenge');?>:&nbsp;
					</div>
					<div class="my_div_body">
						<?php //print $node->reto_al_que_responde;?>
					</div>
				</div>
			</div>
		  </div>
                  -->

                 <?php print get_reto_al_que_responde_fieldset($node)?>

                  <div class="field field-type-text field-field-idea-plazo_del-reto" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php print t('Control Date');?>:&nbsp;
					</div>
					<?php print date('d-m-Y',my_mktime($node->plazo_del_reto)); ?>
				</div>
			</div>
	  	  </div>

                  <div class="field field-type-text field-field-idea-invitados_list" style="float:left;clear:both;">
				<div class="field-items">
					<div class="field-item odd">
						<div class="field-label-inline-first" style="float:left;">
						  <?php print t('Guests');?>:&nbsp;
						</div>
                                                <!--
						<div style="float:left;">
							<ul class="my_supercanal_ul">
							<?php //if(count($node->invitados_list)>0):?>
								<?php //foreach($node->invitados_list as $i=>$p):?>
									<li class="my_supercanal_li"><?php //print $p->username;?>&nbsp;</li>
								<?php //endforeach;?>
							<?php //endif;?>
							</ul>
						</div>
                                                -->
                                                <div class="item-teaser-texto"><?php print get_idea_invitados_string_list($node); ?></div>
					</div>
				</div>
		  </div>

                  <div class="field field-type-text field-field-idea-adheridas_list" style="float:left;clear:both;">
				<div class="field-items">
					<div class="field-item odd">
						<div class="field-label-inline-first" style="float:left;">
						  <?php print t('Supported by');?>:&nbsp;
						</div>
                                                <!--
						<div style="float:left;">
							<ul class="my_supercanal_ul">
							<?php //if(count($node->adheridas_list)>0):?>
								<?php //foreach($node->adheridas_list as $i=>$p):?>
									<li class="my_supercanal_li"><?php //print $p->username;?>&nbsp;</li>
								<?php //endforeach;?>
							<?php //endif;?>
							</ul>
						</div>
                                                -->
                                                <div class="item-teaser-texto" id="id_supported_by_<?php print $node->nid;?>"><?php print get_idea_adheridas_string_list($node); ?></div>
					</div>
				</div>
		  </div>

                  <div class="field field-type-text field-field-idea-subgrupo_list" style="float:left;clear:both;">
				<div class="field-items">
					<div class="field-item odd">
						<div class="field-label-inline-first" style="float:left;">
						  <?php print t('Subgroup');?>:&nbsp;
						</div>
                                                <!--
						<div style="float:left;">
							<ul class="my_supercanal_ul">
							<?php //if(count($node->subgrupo_list)>0):?>
								<?php //foreach($node->subgrupo_list as $i=>$p):?>
									<li class="my_supercanal_li"><?php //print $p->username;?>&nbsp;</li>
								<?php //endforeach;?>
							<?php //endif;?>
							</ul>
						</div>
                                                -->
                                                <div class="item-teaser-texto"><?php print get_idea_subgrupo_string_list($node);?></div>
					</div>
				</div>
		  </div>
		  		 		  
		  <div class="field field-type-text field-field-idea-ficheros_adjuntos" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Attachments');?>:&nbsp;
					</div>									
					<?php print my_get_node_files($node);?>	
				</div>
			</div>
		  </div>
                  
                  <div class="field field-type-text field-field-idea-enlaces" style="float:left;clear:both;">  
                    <?php $links_content=hontza_get_enlaces_view_html($node,0,1);?>
                    <?php if(!empty($links_content)):?> 
                     <h3><?php print t('Links')?></h3> 
                     <?php print $links_content;?>
                    <?php endif;?>
                  </div>
                  <!--table-->
                  <?php endif;?>
		  <?php if(hontza_is_con_botonera()):?>	
		  <div class="n-opciones-item">
                      <!--
                      <div class="item-fuente">			
                        <?php //print idea_origin_link($node);?>        
                      </div>
                      -->
                                
                                <?php //intelsat-2015?>
                                <?php if(!hontza_solr_search_is_usuario_lector()):?>
                                <div class="item-etiquetar">					
                                    <?php print idea_tag_link($node);?>
                                </div>        
                      
                                <div class="item-comentar">
                                    <?php print idea_comment_link($node);?>
                                </div>

                                
                                <?php print link_adherirse($node);?>

                      
                                <?php print link_add_oportunidad($node);?>
	  
                      
                      
                                <div class="item-editar">
                                    <?php print idea_edit_link($node);?>
				</div>
                      
	  			<div class="item-borrar">
                                    <?php print idea_delete_link($node);?>
	  			</div>
                                <?php endif;?>    
                      
                                <!--
	  			<div class="items-coments-page0">
                                -->
                                <div class="items-coments">
        			<?php $node_c_d_w=my_get_node_c_d_w($node)?>
                                <?php print $node_c_d_w;?>    
	  			</div>
                                <?php //intelsat-2015?>
                                <?php print hontza_solr_search_fivestar_botonera($node,1,$node_c_d_w);?>
                    </div><!-- opciones-item-->
                    <?php endif;?>
	  </div><!-- content -->
  <?php //page==0 endif?>      
  <?php endif;?>
<!--          
</div>
-->
</div>