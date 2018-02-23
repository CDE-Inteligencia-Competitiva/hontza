<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?><?php print my_class_primera_proyecto($node,$page);?>">
  <?php //echo print_r($node,1);?>
  <?php $my_user_info=my_get_user_info($node);?>
  <?php if ($page == 0):?>
    <div id="flagtitulo">
      <?php print get_proyecto_simbolo_img();?>
        <div class="f-titulo"><h2<?php print red_copiar_get_title_imported_class($node);?>><?php print l(estrategia_set_title_max_len(htmlkarakter($title)),'node/'.$node->nid,array('query'=>drupal_get_destination()));?></h2></div>
	  <?php print my_help_primera_proyecto($node);?>
    </div>
    <div style="clear:both;width:100%;">
	  <div class="contenedor_left">
              <div class="user_img_left">
                    <?php print $my_user_info['img'];?>
              </div>
              <div class="div_suma_votos_parent">
                  <div class="div_suma_votos">
                    <?php print $node->puntuacion_total;?>
                  </div>
              </div>
          </div>
	  <div id="i-contenedor">
               
	   
				 <!-- gemini-->
				 <div id="my_ffc">
					<div class="item-fecha">
					  <?php print date('d/m/Y',$node->created);?>
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
                                          <?php print hontza_proyecto_resumen($node);?>
                                      </div>
                                    <?php endif; ?>

                                    <div class="div_idea_list_personas">
                                        <label><b><?php print t('Subgroup');?>:</b>&nbsp;</label>

                                        <div class="item-teaser-texto"><?php print get_proyecto_subgrupo_string_list($node); ?></div>
                                    </div>

                                    <div class="div_idea_list_personas">
                                        <label><b><?php print t('Guests');?>:</b>&nbsp;</label>

                                        <div class="item-teaser-texto"><?php print get_idea_invitados_string_list($node); ?></div>
                                    </div>

                                    <div class="div_idea_list_personas">
                                        <label><b><?php print t('Thematic Categories');?>:</b>&nbsp;</label>

                                        <div class="item-teaser-texto"><?php print get_proyecto_categorias_tematicas_string_list($node); ?></div>
                                    </div>

                                  <?php print get_reto_al_que_responde_fieldset($node)?>

                                  <?php print get_numero_socios_fieldset($node)?>

				  <?php print oportunidad_list_camino($node)?>

                                  <?php include('evaluacion-proyecto.tpl.php');?>

				  <?php print my_get_node_files($node);?>	
				  </div><!-- end fcc-->
                                  </div><!-- end i-contenedor-->
                                  
				  <div class="n-opciones-item">
                                                
                                                <?php //intelsat-2015?>
                                                <?php if(!hontza_solr_search_is_usuario_lector()):?>
						<div class="n-item-comentar">
							<?php print proyecto_comment_link($node);?>
						</div>     
						 
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
                                      
                                      
                                                <div class="n-item-editar">
                                                    <?php //print l(t('Oportunidades/Amenazas'),'oportunidad/'.$node->nid,array('query'=>drupal_get_destination()));?>
                                                    <?php print proyecto_edit_link($node);?>
                                                </div>
                                      
						<div class="n-item-borrar">					
						  <?php print proyecto_delete_link($node);?>
						</div>
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
		  <?php if(proyecto_is_ficha_tabla()):?>
                  <?php include('node-proyecto-table-view.tpl.php');?>
                  <?php else:?>	
	   
		   <div class="field field-type-text field-field-proyecto-created" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php print t('Creation Date');?>:&nbsp;
					</div>
					<?php print date('d/m/Y H:i',$node->created);?>
				</div>
			</div>
	  	  </div>

                  <div class="field field-type-text field-field-estado_del_proyecto_label" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Project Status');?>:&nbsp;
					</div>
					<?php print $node->estado_del_proyecto_label; ?>
				</div>
			</div>
		  </div>
                  
                  <div class="field field-type-text field-field-proyecto-argumentacion" style="float:left;clear:both;">
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

                  <div class="field field-type-text field-field-proyecto-categorias-tematicas" style="float:left;clear:both;">
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

                   <?php print get_reto_al_que_responde_fieldset($node)?>

                   <?php print oportunidad_list_camino($node)?>

		  <?php include('evaluacion-proyecto.tpl.php');?>
		  
		  <div class="field field-type-text field-field-proyecto-acronimo" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Acronym');?>:&nbsp;
					</div>									
					<?php print $node->acronimo;?>  
				</div>
			</div>
		  </div>
		  
		  <div class="field field-type-text field-field-proyecto-imagen_o-logo" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Image or logo');?>:&nbsp;
					</div>									
					<?php if(isset($node->field_imagen_o_logo[0]) && isset($node->field_imagen_o_logo[0]['view'])):?>	
						<?php print $node->field_imagen_o_logo[0]['view'];?> 
					<?php endif;?>	 
				</div>
			</div>
		  </div>
		  
		  <div class="field field-type-text field-field-proyecto-fases" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Phases');?>:&nbsp;
					</div>									
					<?php print $node->fases;?>  
				</div>
			</div>
		  </div>

                  <div class="field field-type-text field-field-proyecto-duracion_estimada" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Estimated duration of project');?>:&nbsp;
					</div>
					<?php print $node->duracion_estimada;?>
				</div>
			</div>
		  </div>
		  
		  <div class="field field-type-text field-field-proyecto-experiencia_disponible" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Knowledge available');?>:&nbsp;
					</div>									
					<?php print $node->experiencia_disponible;?>  
				</div>
			</div>
		  </div>
		  
		   <div class="field field-type-text field-field-proyecto-experiencia_necesaria" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Knowledge necessary');?>:&nbsp;
					</div>									
					<?php print $node->experiencia_necesaria;?>  
				</div>
			</div>
		  </div>

                  <!--
		  <div class="field field-type-text field-field-proyecto-tipo_tid" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php //print t('Type');?>:&nbsp;
					</div>									
					<?php //print $node->tipo_name;?>
				</div>
			</div>
		  </div>
                  -->

                  <!--
		  <div class="field field-type-text field-field-proyecto-numero_socios" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php //print t('Number of partners required');?>:&nbsp;
					</div>									
					<?php //print $node->numero_socios;?>
				</div>
			</div>
		  </div>
                  -->
		  
		   <div class="field field-type-text field-field-proyecto-socios_involucrados" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Partners involved');?>:&nbsp;
					</div>									
					<?php print $node->socios_involucrados;?>  
				</div>
			</div>
		  </div>
		  
		   <div class="field field-type-text field-field-proyecto-socios_contactados" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Partners contacted');?>:&nbsp;
					</div>									
					<?php print $node->socios_contactados;?>  
				</div>
			</div>
		  </div>
		  
		   <div class="field field-type-text field-field-proyecto-socios_potenciales" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Potential partners');?>:&nbsp;
					</div>									
					<?php print $node->socios_potenciales;?>  
				</div>
			</div>
		  </div>

                  <!--
		   <div class="field field-type-text field-field-proyecto-tipo2_tid" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php //print t('Type2');?>:&nbsp;
					</div>									
					<?php //print $node->tipo2_name;?>
				</div>
			</div>
		  </div>
                  -->

                  <!--
		  <div class="field field-type-text field-field-proyecto-numero_validadores" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php //print t('Number of validators');?>:&nbsp;
					</div>									
					<?php //print $node->numero_validadores;?>
				</div>
			</div>
		  </div>
                  -->

                 

                 <!--
                 <div class="field field-type-text field-field-proyecto-reto_al_que_responde" style="float:left;clear:both;">
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

                  

                  <?php print get_numero_socios_fieldset($node)?>

                  <div class="field field-type-text field-field-proyecto-plazo_del-reto" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first">
					  <?php print t('Control Date');?>:&nbsp;
					</div>
					<?php print date('d-m-Y',my_mktime($node->plazo_del_reto)); ?>
				</div>
			</div>
	  	  </div>

                  <div class="field field-type-text field-field-proyecto-subgrupo_list" style="float:left;clear:both;">
				<div class="field-items">
					<div class="field-item odd">
						<div class="field-label-inline-first" style="float:left;">
						  <?php print t('Subgroup');?>:&nbsp;
						</div>
						<div style="float:left;">
							<ul class="my_supercanal_ul">
							<?php if(count($node->personas_list)>0):?>
								<?php foreach($node->personas_list as $i=>$p):?>
									<li class="my_supercanal_li"><?php print $p->username;?>&nbsp;</li>
								<?php endforeach;?>
							<?php endif;?>
							</ul>
						</div>
					</div>
				</div>
		  </div>

                  <div class="field field-type-text field-field-proyecto-invitados_list" style="float:left;clear:both;">
				<div class="field-items">
					<div class="field-item odd">
						<div class="field-label-inline-first" style="float:left;">
						  <?php print t('Guests');?>:&nbsp;
						</div>
						<div style="float:left;">
							<ul class="my_supercanal_ul">
							<?php if(count($node->invitados_list)>0):?>
								<?php foreach($node->invitados_list as $i=>$p):?>
									<li class="my_supercanal_li"><?php print $p->username;?>&nbsp;</li>
								<?php endforeach;?>
							<?php endif;?>
							</ul>
						</div>
					</div>
				</div>
		  </div>
                  		 
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
                                
                                <?php //intelsat-2015?>
                                <?php if(!hontza_solr_search_is_usuario_lector()):?>
  				<div class="item-comentar">
					<?php print proyecto_comment_link($node);?>
                                </div>
      			
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
                                      
                      
                      
                                <div class="item-editar">
					 <?php print proyecto_edit_link($node);?>
                                </div>
    
	  			<div class="item-borrar">
                                    <?php print proyecto_delete_link($node);?>
	  			</div>
                                <?php endif;?>
                                <!--
	  			<div class="items-coments-page0">
                                -->
                                <div class="items-coments">
        			<?php print my_get_node_c_d_w($node)?>
	  			</div>
                </div><!-- opciones-item-->
                <?php endif;?>
  </div><!-- content -->		  
  <?php //page==0 endif?>
  <?php endif;?>
</div>  