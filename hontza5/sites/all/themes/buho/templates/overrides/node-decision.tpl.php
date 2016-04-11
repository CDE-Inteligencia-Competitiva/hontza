<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?><?php print my_class_primera_decision($node,$page);?>">
  <?php //echo print_r($node,1);?>
  <?php $my_user_info=my_get_user_info($node);?>
  <?php if ($page == 0):?>
    <div id="flagtitulo">
      <?php print get_decision_simbolo_img();?>
      <div class="f-titulo"><h2><?php print l(estrategia_set_title_max_len(htmlkarakter($title)),'node/'.$node->nid,array('query'=>drupal_get_destination()));?></h2></div>
	  <?php print my_help_primera_decision($node);?>
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
					</div>	-->
                                 <!--       
				 </div>
                                 -->
				
                                    <?php if(hontza_node_has_body($node)): ?>
                                      <div class="item-teaser-texto">
                                          <?php print hontza_decision_resumen($node);?>
                                      </div>
                                    <?php endif; ?>
                                      
                                    <?php if(!estrategia_is_grupo_estrellas()):?>    
                                    <div class="div_idea_list_personas">
                                        <label><b><?php print t('Value of Decision');?>:</b>&nbsp;</label>

                                        <div class="item-teaser-texto">
                                            <?php //print $node->valor_decision_name; ?>
                                            <?php print get_eval_label($node->valor_decision, 'valor_decision');?>
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
				  
				  <?php print despliegue_list_camino($node)?>
                                 
				  <?php print my_get_node_files($node);?>	
				  </div><!-- end fcc-->
                                  </div><!-- end i-contenedor-->
				  <div class="n-opciones-item"<?php print estrategia_get_opciones_item_style();?>>
                                            <?php //intelsat-2015 ?>
                                            <?php if(!hontza_solr_search_is_usuario_lector()):?>
                                                <div class="n-item-comentar">
                                                    <?php print decision_comment_link($node);?>
						</div>
                                      
                                                <?php if(informacion_is_admin_content()):?>
                                                
						 <div class="n-item-informacion">
                                                
	  						<?php //print l(t('Add key Question'),'node/add/informacion/'.$node->nid)?>
                                                        <?php print informacion_add_link($node);?>
	  					</div>
                                                <?php endif;?>
                                      
                                                <?php if(decision_is_admin_content()):?>
                                                <div class="n-item-editar">
							<?php print decision_edit_link($node);?>
						</div>
                                                <?php endif;?>

						     
						 
                                                <?php if(decision_is_admin_content()):?>
						<div class="n-item-borrar">					
						  <?php print decision_delete_link($node);?>
						</div>
                                                <?php endif;?>
                                            <?php endif;?>        
                                                
																										
						  <div class="items-coments">						
							<?php print my_get_node_c_d_w($node)?>
						  </div>
																  
				  <?php //end n-opciones-item?>
				  </div>
          <!--                        
	  </div>
          -->
	</div><!-- <div style="clear:both;width:100%;">-->  
    <?php //page==1?>
  <?php else:?>
  	<div class="content">	  	  
		  <?php $my_user_info=my_get_user_info($node);?>
	 
		  <div style="float:left;clear:both;">
				<?php print $my_user_info['img'];?> 
		  </div>
                  <?php if(estrategia_is_ficha_tabla()):?>
                  <?php include('node-decision_table_view.tpl.php');?>
                  <?php else:?>	   
                   <?php if(estrategia_is_grupo_estrellas()):?>
                   <div class="field field-type-text field-field-decision-puntuacion_total" style="float:left;clear:both;">
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
		  
		   <div class="field field-type-text field-field-decision-created" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php print t('Creation Date');?>:&nbsp;
					</div>
					<?php print date('d-m-Y H:i',$node->created); ?>
				</div>
			</div>
	  	  </div>
		 
                  <div class="field field-type-text field-field-decision-argumentacion" style="float:left;clear:both;">
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

                   
                  <?php if(!estrategia_is_grupo_estrellas()):?>
		  <div class="field field-type-text field-field-decision-valor_decision" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Value of Decision');?>:&nbsp;
					</div>									
					<?php //print $node->valor_decision_name;?>
                                        <?php print get_eval_label($node->valor_decision, 'valor_decision');?>
				</div>
			</div>
		  </div>
                  <?php endif;?>

                  <div class="field field-type-text field-field-decision-grupo_seguimiento_name" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Working Group');?>:&nbsp;
					</div>
                                        <?php //if(!empty($node->grupo_seguimiento_nid)):?>
                                            <?php print $node->grupo_seguimiento_link;?>
                                        <?php //endif;?>
				</div>
			</div>
		  </div>


                 <div class="field field-type-text field-field-decision-fecha_cumplimiento" style="float:left;clear:both;">
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
                    <div class="field field-type-text field-field-decision-no-control-date" style="float:left;clear:both;">
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
                  <div class="field field-type-text field-field-decision-fivestar" style="float:left;clear:both;">
                        <div class="item-fivestar" style="padding-left:0px;">
                                       <div style="float:left;">

                                       <?php print traducir_average($node->content['fivestar_widget']['#value']); ?>			    

                                       </div>
                         </div>   
                   </div>
                   <?php endif;?>

		   <div class="field field-type-text field-field-decision-ficheros_adjuntos" style="float:left;clear:both;">
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
					<?php print decision_comment_link($node);?>
                                </div>
                      
                                <?php if(informacion_is_admin_content()):?>
	  			<div class="item-informacion">
	  				<?php print informacion_add_link($node);?>
	  			</div>
                                <?php endif;?>
                      
                                <?php if(decision_is_admin_content()):?>
                                <div class="item-editar">
                                    <?php print decision_edit_link($node);?>
				</div>
                                <?php endif;?>

  				
      			
                                <?php if(decision_is_admin_content()):?>    
	  			<div class="item-borrar">
                                    <?php print decision_delete_link($node);?>
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