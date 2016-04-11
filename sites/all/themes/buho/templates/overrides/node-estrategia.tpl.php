<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?><?php print my_class_primera_estrategia($node,$page);?>">
  <?php //echo print_r($node,1);?>
  <?php $my_user_info=my_get_user_info($node);?>
  <?php if ($page == 0):?>
    <div id="flagtitulo">
      <?php print get_estrategia_simbolo_img();?>
        <div class="f-titulo"><h2<?php print red_copiar_get_title_imported_class($node);?>><?php print l(estrategia_set_title_max_len(htmlkarakter($title)),'node/'.$node->nid,array('query'=>drupal_get_destination()));?></h2></div>
	  <?php print my_help_primera_estrategia($node);?>
    </div>
    <div style="clear:both;width:100%;">
	  <div class="contenedor_left">
              <div class="user_img_left">
                    <?php print $my_user_info['img'];?>
              </div>
              <?php if(estrategia_is_grupo_estrellas()):?>
              <div class="div_suma_votos_parent">
                  <div class="div_suma_votos">
                    <?php print $node->suma_votos;?>
                  </div>
              </div>
              <?php endif;?>
          </div>
	  <div id="i-contenedor">	
                  		 <!-- gemini-->
				 <div id="ffc">
					<div class="item-fecha">
					  <?php print date('d/m/Y',$node->created); ?>
					</div>
					<!--
					<div class="item-canal">
					  <span class="etiqueta-gris"><?php //print t('Persona origen')?>: </span> <?php //print $node->persona_origen_name; ?>
					</div>-->
                                 <!--       
				 </div>
				 -->
				
				  <?php if(hontza_node_has_body($node)): ?>                                       
                                      <div class="item-teaser-texto">
                                          <?php print hontza_estrategia_resumen($node);?>
                                      </div>
                                    <?php endif; ?>
                                    <?php if(!estrategia_is_grupo_estrellas()):?>    
                                    <div class="div_idea_list_personas">
                                        <label><b><?php print t('Challenge Value');?>:</b>&nbsp;</label>

                                        <div class="item-teaser-texto">
                                            <?php //print $node->valor_reto_name; ?>
                                            <?php print get_eval_label($node->valor_reto,'valor_reto');?>
                                        </div>
                                    </div>
                                    <div class="div_idea_list_personas">
                                        <label><b><?php print t('Importance');?>:</b>&nbsp;</label>

                                        <div class="item-teaser-texto">
                                            <?php print get_eval_label($node->importancia_reto,'importancia_reto');?>
                                        </div>
                                    </div>
                                    <div class="div_idea_list_personas">
                                        <label><b><?php print t('Feasibility');?>:</b>&nbsp;</label>

                                        <div class="item-teaser-texto">
                                            <?php print get_eval_label($node->facilidad_reto,'facilidad_reto');?>
                                        </div>
                                    </div>  
                                    <?php endif;?>  
                                      
                                    <div class="div_idea_list_personas">
                                        <label><b><?php print t('Working Group');?>:</b>&nbsp;</label>

                                        <div class="item-teaser-texto"><?php print $node->grupo_seguimiento_link;?></div>
                                    </div>
                                      
                                    <div class="div_idea_list_personas">
                                        <label><b><?php print t('Control Date');?>:</b>&nbsp;</label>

                                        <div class="item-teaser-texto"><?php print date('d-m-Y',my_mktime($node->fecha_cumplimiento)); ?></div>
                                    </div>

                                      
                                    <?php if(estrategia_is_grupo_estrellas()):?>
                                      <div class="div_idea_list_personas">
                                        <div class="item-fivestar" style="padding-left:0px;">
                                                       <div style="float:left;">

                                                       <?php print traducir_average($node->content['fivestar_widget']['#value']); ?>			    

                                                       </div>
                                         </div>   
                                    </div>
                                    <?php endif;?>  

				  <?php print my_get_node_files($node);?>	
				  </div><!-- end fcc-->
                                  </div><!-- end i-contenedor-->
				  <div class="n-opciones-item"<?php print estrategia_get_opciones_item_style();?>>
                                                <?php //intelsat-2015 ?>
                                                <?php if(!hontza_solr_search_is_usuario_lector()):?>
						<div class="n-item-comentar">
							<?php //print l(t('Comment'),"comment/reply/".$node->nid,array('fragment'=>'comment-form','query'=>drupal_get_destination()));?>
                                                        <?php print estrategia_comment_link($node);?>
                                                </div>
                                      
                                                <?php if(despliegue_is_admin_content()):?>
						 <div class="n-item-despliegue">
	  						<?php //print l(t('Add SubChallenge'),'despliegues/'.$node->nid)?>
                                                        <?php //print l(t('Add SubChallenge'),'node/add/despliegue/'.$node->nid)?>
                                                        <?php print despliegue_add_subchallenge_link($node);?>
	  					  </div>
                                                <?php endif;?>  
						 
                                                <?php //intelsat-2016 ?>
                                                <?php if(red_copiar_is_copiar_activado()):?>
                                                <?php if(compartir_documentos_custom_access()):?>
                                                <div class="n-item-copiar-nodo">
                                                    <?php //print l(t('Edit'),"node/".$node->nid.'/edit');?>
                                                    <?php //print red_copiar_wiki_copiar_link($node);?>
                                                    <?php print compartir_documentos_copiar_link($node);?>
                                                </div>
                                                <?php if(compartir_documentos_is_show_reset_link($node)):?>
                                                <div class="n-item-reset-copiar-nodo">
                                                    <?php print compartir_documentos_reset_link($node);?>
                                                </div>
                                                <?php endif;?>
                                                <?php endif;?>
                                                <?php endif;?>
                                      
                                                <?php if(estrategia_is_admin_content()):?>
                                                <div class="n-item-editar">
							<?php //print l(t('Edit'),"node/".$node->nid.'/edit');?>
                                                        <?php print estrategia_edit_link($node);?>
						</div> 
                                                <?php endif;?>
                                      
                                                <?php if(estrategia_is_admin_content()):?>
						<div class="n-item-borrar">					
						  <?php //print l(t('Delete'),'node/'.$node->nid.'/delete',array('query'=>drupal_get_destination()));?>
                                                  <?php print estrategia_delete_link($node);?>  
						</div>
						<?php endif;?>
                                                <?php endif;?>
                                                
                                                
																										
						  <div class="items-coments">						
							<?php print my_get_node_c_d_w($node)?>
						  </div>
																  
				  <?php /*end n-opciones-item*/?>
				  </div>
		
	<!--  		  
	</div>
        -->
    <?php //page==1?>
  <?php else:?>
  	<div class="content">	  	  
		  <?php $my_user_info=my_get_user_info($node);?>
	 
		  <div style="float:left;clear:both;">
				<?php print $my_user_info['img'];?> 
		  </div>
                  <?php if(estrategia_is_ficha_tabla()):?>
                  <?php include('node-estrategia_table_view.tpl.php');?>
                  <?php else:?>	
                  <?php if(estrategia_is_grupo_estrellas()):?>
                   <div class="field field-type-text field-field-estrategia-puntuacion_total" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php print t('Total score');?>:&nbsp;
					</div>
					<?php print $node->suma_votos;?>
				</div>
			</div>
	  	  </div>
                  <?php endif;?>
		  
		   <div class="field field-type-text field-field-estrategia-created" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php print t('Creation Date');?>:&nbsp;
					</div>
					<?php print date('d-m-Y H:i',$node->created); ?>
				</div>
			</div>
	  	  </div>
		  		  		  
		  <div class="field field-type-text field-field-estrategia-argumentacion" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Full Text');?>:&nbsp;
					</div>									
					<div class="my_div_body">						
                                                <?php print $node->content['body']['#value'] ?>
					</div>
				</div>
			</div>
		  </div>
		  
                  <?php if(!estrategia_is_grupo_estrellas()):?>
		  <div class="field field-type-text field-field-estrategia-valor_reto" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Challenge Value');?>:&nbsp;
					</div>									
					<?php //print $node->valor_reto_name;?>
                                        <?php print get_eval_label($node->valor_reto,'valor_reto');?>
				</div>
			</div>
		  </div>
                  <div class="field field-type-text field-field-estrategia-importancia_reto" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Importance');?>:&nbsp;
					</div>									
					<?php print get_eval_label($node->importancia_reto,'importancia_reto');?>
				</div>
			</div>
		  </div>
                  <div class="field field-type-text field-field-estrategia-facilidad_reto" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Feasibility');?>:&nbsp;
					</div>									
					<?php print get_eval_label($node->facilidad_reto,'facilidad_reto');?>
				</div>
			</div>
		  </div>
                  <?php endif;?>

                  <div class="field field-type-text field-field-estrategia-grupo_seguimiento_name" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Working Group');?>:&nbsp;
					</div>
					<?php print $node->grupo_seguimiento_link;?>
				</div>
			</div>
		  </div>

                   <div class="field field-type-text field-field-estrategia-fecha_cumplimiento" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php print t('Control Date');?>:&nbsp;
					</div>
					<?php //print date('d-m-Y',my_mktime($node->fecha_cumplimiento)); ?>
                                        <?php print estrategia_inc_get_control_date($node);?>
				</div>
			</div>
	  	  </div>
                  <!--
                  <div class="field field-type-text field-field-estrategia-no-control-date" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php //print t('No Control Date');?>:&nbsp;
					</div>
					<?php //print informacion_get_no_control_date_label($node->no_control_date); ?>
				</div>
			</div>
	  	  </div>
                  -->
                  <?php if(estrategia_is_grupo_estrellas()):?>
                  <div class="field field-type-text field-field-estrategia-fivestar" style="float:left;clear:both;">
                        <div class="item-fivestar" style="padding-left:0px;">
                                       <div style="float:left;">

                                       <?php print traducir_average($node->content['fivestar_widget']['#value']); ?>			    

                                       </div>
                         </div>   
                   </div>
                   <?php endif;?>
            

		   <div class="field field-type-text field-field-proyecto-ficheros_adjuntos" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Attachments');?>:&nbsp;
					</div>									
					<?php print my_get_node_files($node);?>	
				</div>
			</div>
		  </div>
                  <!--table-->
                  <?php endif;?>
                  <?php if(hontza_is_con_botonera()):?>
		  <div class="n-opciones-item">
                                
                            <?php //intelsat-2015 ?>
                            <?php if(!hontza_solr_search_is_usuario_lector()):?>
  				<div class="item-comentar">
                                    <?php //print l(t('Comment'),'comment/reply/'.$node->nid,array('fragment'=>'comment-form'));?>
                                    <?php print estrategia_comment_link($node);?>
                                </div>
      			
                                <?php if(despliegue_is_admin_content()):?>
	  			<div class="item-despliegue">
	  				<?php //print l(t('Add SubChallenge'),'despliegues/'.$node->nid)?>
                                        <?php //print l(t('Add SubChallenge'),'node/add/despliegue/'.$node->nid)?>
                                        <?php print despliegue_add_subchallenge_link($node);?>
	  			</div>
                                <?php endif;?>
                      
                                <?php //intelsat-2016 ?>
                                <?php if(red_copiar_is_copiar_activado()):?>
                                <?php if(compartir_documentos_custom_access()):?>
                                <div class="item-copiar-nodo">
                                <?php //print l(t('Edit'),"node/".$node->nid.'/edit');?>
                                <?php //print red_copiar_wiki_copiar_link($node);?>
                                <?php print compartir_documentos_copiar_link($node);?>
                                </div>
                                <?php if(compartir_documentos_is_show_reset_link($node)):?>
                                <div class="item-reset-copiar-nodo">
                                    <?php print compartir_documentos_reset_link($node);?>
                                </div>
                                <?php endif;?>
                                <?php endif;?>
                                <?php endif;?>                  
                      
                                <?php if(estrategia_is_admin_content()):?>
                                <div class="item-editar">
                                    <?php //print l(t('Edit'),"node/".$node->nid.'/edit');?>
                                    <?php print estrategia_edit_link($node);?>
				</div>
                                <?php endif;?>
                                
                                <?php if(estrategia_is_admin_content()):?>
	  			<div class="item-borrar">
	  				<?php //print l(t('Delete'),'node/'.$node->nid.'/delete')?>
                                        <?php print estrategia_delete_link($node);?>
	  			</div>
                                <?php endif;?>
                            <?php endif;?>    
                               
                                <!--
	  			<div class="items-coments-page0">
                                -->
                                <div class="items-coments">
        			<?php print my_get_node_c_d_w($node)?>
	  			</div>
                   </div>
                   <?php endif;?>
	</div>	  <!-- content-->
    <?php //page==0 endif?>
  <?php endif;?>
</div>
</div>