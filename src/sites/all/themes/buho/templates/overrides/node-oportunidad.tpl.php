<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?><?php print my_class_primera_oportunidad($node,$page);?>">
  <?php //echo print_r($node,1);?>
  <?php $my_user_info=my_get_user_info($node);?>
  <?php if ($page == 0):?>
  <div id="flagtitulo">
       <?php print get_oportunidad_simbolo_img();?>
      <div class="f-titulo"><h2><?php print l(estrategia_set_title_max_len(htmlkarakter($title)),'node/'.$node->nid,array('query'=>drupal_get_destination()));?></h2></div>
	  <?php print my_help_primera_oportunidad($node);?>
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
			 <div id="my_ffc">
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
                                <?php print hontza_oportunidad_resumen($node);?>
                            </div>
                         <?php endif; ?>

                         <div class="div_idea_list_personas">
                            <label><b><?php print t('Subgroup');?>:</b>&nbsp;</label>

                            <div class="item-teaser-texto" style="float:none;"><?php print get_oportunidad_subgrupo_string_list($node); ?></div>
                        </div>

                        <div class="div_idea_list_personas">
                            <label><b><?php print t('Guests');?>:</b>&nbsp;</label>

                            <div class="item-teaser-texto" style="float:none;" ><?php print get_idea_invitados_string_list($node); ?></div>
                        </div>

                        <div class="div_idea_list_personas">
                            <label><b><?php print t('Supported by');?>:</b>&nbsp;</label>

                            <div class="item-teaser-texto" style="float:none;" id="id_oportunidad_supported_by_<?php print $node->nid;?>"><?php print get_oportunidad_adheridas_string_list($node); ?></div>
                        </div>

                        <div class="div_idea_list_personas">
                            <label><b><?php print t('Thematic Categories');?>:</b>&nbsp;</label>

                            <div class="item-teaser-texto"><?php print get_oportunidad_categorias_tematicas_string_list($node); ?></div>
                        </div>

                        <?php print get_reto_al_que_responde_fieldset($node)?>
                        <!-- 
                        <div class="item-fivestar">

                                <?php //if($node->es_mi_oportunidad):?>

                                    <div>

                                    <?php //print $node->my_stars;?>
                                    </div>    
                                <?php //else:?>

                                    <div style="float:left;">

                                    <?php //print traducir_average($node->content['fivestar_widget']['#value']); ?>
                                    </div>    
			  	<?php //endif;?>
                                


			  </div>
                          -->  
			  <?php print oportunidad_list_camino($node)?>
			  
			  <?php print my_get_node_files($node);?>	
			  </div><!-- end fcc-->
                          </div><!-- end i-contenedor-->
			  <div class="n-opciones-item"> 
	 				
                                        <?php //intelsat-2015?>
                                        <?php if(!hontza_solr_search_is_usuario_lector()):?>
                                        <div class="n-item-comentar">
			  			<?php print oportunidad_comment_link($node);?>
					</div>
                              
                                         <?php print link_adherirse_oportunidad($node,'n');?>
                                        
                                         <?php print link_add_proyecto($node,'n');?>
                              
					 
                                        <div class="n-item-editar">
					  	<?php print oportunidad_edit_link($node);?>
					</div>
				  
					<div class="n-item-borrar">					
					  <?php print oportunidad_delete_link($node);?>
					</div>
                                        <?php endif;?>
                                        <!--
					 <div class="n-item-proyectos">
	  					<?php //print l(t('Add Project'),'proyectos/'.$node->nid)?>
                                                <?php //print l(t('Add Project'),'node/add/proyecto/'.$node->nid)?>
	  				</div>
                                        -->

                                          
									
					  <div class="items-coments">						
						<?php print my_get_node_c_d_w($node)?>
					  </div>
                                          <?php //intelsat-2015?>
                                          <?php print hontza_solr_search_fivestar_botonera($node,1);?>  
					  										  
			  <?php //end n-opciones-item?>
			  </div>
	 <!--
	 </div>
         -->
  </div>	  
  <?php //page==1?>
  <?php else:?>
  	  <!--
	  <div id="flagtitulo">
		  <div class="f-titulo"> <h2><?php //print $title ?></h2></div>
	  </div>
	  -->
	  <div class="content">	  	  
		  <?php $my_user_info=my_get_user_info($node);?>
	 
		  <div style="float:left;clear:both;">
				<?php print $my_user_info['img'];?> 
		  </div>
		  <?php if(oportunidad_is_ficha_tabla()):?>
                  <?php include('node-oportunidad-table-view.tpl.php');?>
                  <?php else:?>	
		  <!--
		  <div class="field field-type-text field-field-oportunidad-type" style="float:left;clear:both;">
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
		  
		  <div class="field field-type-text field-field-oportunidad-created" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php print t('Creation Date');?>:&nbsp;
					</div>
					<?php print date('d/m/Y H:i',$node->created); ?>
				</div>
			</div>
	  	  </div>
		 
                  <div class="field field-type-text field-field-oportunidad-argumentacion" style="float:left;clear:both;">
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

                  <div class="field field-type-text field-field-oportunidad-evaluacion_oportunidad" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<!--
					<div class="field-label-inline-first" style="float:left;">
					  <?php //print t('Attachments');?>:&nbsp;
					</div>
					-->
                                        <!--
					 <div class="item-fivestar">
						<?php //if($node->es_mi_oportunidad):?>
                                                <div>
                                                    <?php print $node->my_stars;?>
                                                <?php //else:?>
                                                <div style="float:left;">
                                                    <?php //print traducir_average($node->content['fivestar_widget']['#value']); ?>
						<?php //endif;?>


                                                 </div>
		  			</div>
                                        -->
				</div>
			</div>
		  </div>

		  <div class="field field-type-text field-field-oportunidad-puntuacion_total" style="float:left;clear:both;">
				<div class="field-items">
					<div class="field-item odd">
						<div class="field-label-inline-first" style="float:left;">
						  <?php print t('Total score');?>:&nbsp;
						</div>
						<?php print $node->suma_votos;?>
					</div>
				</div>
		  </div>

		  <div class="field field-type-text field-field-oportunidad-personas_list" style="float:left;clear:both;">
				<div class="field-items">
					<div class="field-item odd">
						<div class="field-label-inline-first" style="float:left;">
						  <?php print t('Subgroup');?>:&nbsp;
						</div>
						<!--
                                                <div style="float:left;">
							<ul class="my_supercanal_ul">
							<?php //if(count($node->personas_list)>0):?>
								<?php //foreach($node->personas_list as $i=>$p):?>
									<li class="my_supercanal_li"><?php //print $p->username;?>&nbsp;</li> 
								<?php //endforeach;?>
							<?php //endif;?>
							</ul>
						</div>
                                                -->
                                                <div class="item-teaser-texto"><?php print get_oportunidad_subgrupo_string_list($node); ?></div>
					</div>
				</div>
		  </div>

                  <div class="field field-type-text field-field-oportunidad-invitados_list" style="float:left;clear:both;">
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

                   <div class="field field-type-text field-field-oportunidad-adheridas_list" style="float:left;clear:both;">
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
                                                <div class="item-teaser-texto" id="id_oportunidad_supported_by_<?php print $node->nid;?>"><?php print get_oportunidad_adheridas_string_list($node); ?></div>
                        		</div>
				</div>
		  </div>

                   <div class="field field-type-text field-field-oportunidad-categorias-tematicas" style="float:left;clear:both;">
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
                  		  
		  <div class="field field-type-text field-field-oportunidad-beneficios_riesgos" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Benefits that are achieved');?>:&nbsp;
					</div>									
					<div class="my_div_body">
						<?php print $node->beneficios_riesgos;?>  
					</div>
				</div>
			</div>
		  </div>

                  <fieldset class="oportunidad_view_fieldset">
		  	<legend><?php print t('Rating');?></legend>
			<div class="fieldset-wrapper">
                                <!--
				<div class="field field-type-text field-field-oportunidad-eval_amenaza div_eval">
					<div class="field-items">
						<div class="field-item odd">
							<div class="field-label-inline-first" style="float:left;">
							  <?php //print t('Evaluación de la amenaza');?>:&nbsp;
							</div>
							<?php //print $node->eval_accesibilidad;?>
							<?php //print my_create_stars_view($node->eval_amenaza,0);?>
						</div>
					</div>
		  		</div>
                                -->

				<div class="field field-type-text field-field-oportunidad-eval_oportunidad div_eval">
					<div class="field-items">
						<div class="field-item odd">
							<div class="field-label-inline-first" style="float:left;">
							  <?php print t('Evaluation of the opportunity');?>:&nbsp;
							</div>
							<?php //print $node->eval_accesibilidad;?>
                                                        <?php print my_create_stars_view($node->eval_oportunidad,2,'eval_oportunidad');?>
						</div>
					</div>
		  		</div>
                        </div>
                  </fieldset>
		  
		  <div class="field field-type-text field-field-oportunidad-parte_del_negocio" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Application to Bussiness');?>:&nbsp;
					</div>									
					<div class="my_div_body">
						<?php print $node->parte_del_negocio;?>  
					</div>
				</div>
			</div>
		  </div>
                  <!--
                  <div class="field field-type-text field-field-oportunidad-reto_al_que_responde" style="float:left;clear:both;">
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
                  
                  <?php print oportunidad_list_camino($node)?>

                  <div class="field field-type-text field-field-oportunidad-plazo_del-reto" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php print t('Control Date');?>:&nbsp;
					</div>
					<?php print date('d/m/Y',my_mktime($node->plazo_del_reto));?>
				</div>
			</div>
	  	  </div>

                  
		  
		   <div class="field field-type-text field-field-oportunidad-ficheros_adjuntos" style="float:left;clear:both;">
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
		  <div class="n-opciones-item">
                                <?php //intelsat-2015?>
                                <?php if(!hontza_solr_search_is_usuario_lector()):?>
  				<div class="item-comentar">
					<?php print oportunidad_comment_link($node);?>
                                </div>
      			
    
                                <?php print link_adherirse_oportunidad($node);?> 
                                
                                
                                <?php print link_add_proyecto($node);?>
                      
                                <div class="item-editar">
                                            <?php //print l(t('Oportunidades/Amenazas'),'oportunidad/'.$node->nid,array('query'=>drupal_get_destination()));?>
					    <?php print oportunidad_edit_link($node);?>
                                </div>
                      
	  			<div class="item-borrar">
                                    <?php print oportunidad_delete_link($node);?>
	  			</div>
                                <?php endif;?>    
                      
                                <!--
	  			<div class="item-proyectos">
	  				<?php //print l(t('Add Project'),'proyectos/'.$node->nid)?>
                                        <?php //print l(t('Add Project'),'node/add/proyecto/'.$node->nid)?>
	  			</div>
                                -->

                               

                                 
                                <!--
	  			<div class="items-coments-page0">
                                -->
                                <div class="items-coments">
        			<?php print my_get_node_c_d_w($node)?>
	  			</div>
                                <?php //intelsat-2015?>
                                <?php print hontza_solr_search_fivestar_botonera($node,1);?>  
    	   </div><!-- opciones-item-->	
	  </div><!-- content -->
  <?php //page==0 endif?>
  <?php endif;?>
</div>  