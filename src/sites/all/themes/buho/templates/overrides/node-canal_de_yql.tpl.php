<?php //echo print_r($node,1);?>
<?php if($page==0):?>
	<?php //print 'page=0 node-canal_de_yql.tpl'?>
<?php endif;?>
<?php if($page==1):?>
<?php //echo print_r($node,1);exit();?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?> clear-block">
<?php $my_user_info=my_get_user_info($node);?>
<div class="content">
    <?php $my_user_info=my_get_user_info($node);?>
    <?php $my_node=node_load($node->nid);?>
 
	  <div style="float:left;clear:both;">
			<?php print $my_user_info['img'];?> 
	  </div>
	  <?php if(hontza_canal_rss_is_canal_ficha_tabla()):?>            
            <?php include('node-canal_de_yql_table_view.tpl.php');?>
          <?php else:?>
          <?php if(!red_is_show_canal_title()):?>
          <div class="field field-type-text field-field-supercanal-creator" style="float:left;clear:both;">
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
    
    
	  <div class="field field-type-text field-canal_de_yql-created" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Creation Date');?>:&nbsp;
				</div>
				<?php print date('d-m-Y H:i',$node->created); ?>
			</div>
		</div>
	  </div>

          <div class="field field-type-text field-canal_de_yql_last_update" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php //print t('Last update');?>
                                  <?php print t('Date of last download');?>:&nbsp;
				</div>
				<?php print get_canal_last_update_date_format($node->nid); ?>
			</div>
		</div>
	  </div>
    
          <div class="field field-type-text field-canal_de_yql_last_import_time" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Date of last update');?>:&nbsp;
				</div>
				<?php print get_canal_last_import_time_format($node); ?>
			</div>
		</div>
	  </div>
    
          <?php if(hontza_solr_is_solr_activado()):?>
          <div class="field field-type-text field-canal_de_yql-source-tid" style="float:left;clear:both;">
		<div class="field-items">			
			<div class="field-item odd">			
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Source Type');?>:&nbsp;
				</div>					
				<?php $source_array=hontza_solr_canal_source_term_array($node->nid);?>
                                <?php if (!empty($source_array)): ?>
                                    <div class="terms terms-inline" style="margin-top:0px;">
                                        <?php print hontza_solr_set_canal_source_type_terms_ul($source_array);?>
                                    </div>
                                <?php endif;?>
			</div>
		</div>
	  </div>
          <?php endif;?>
	  
	   <div class="field field-type-text field-canal_de_yql-clasificaciones" style="float:left;clear:both;">
		<div class="field-items">			
			<div class="field-item odd">			
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Thematic Categories');?>:&nbsp;
				</div>					
				  <?php if ($terms): ?>
                                        <div class="terms terms-inline" style="margin-top:0px;">
                                            <?php $new_terms=hontza_set_terms_link($node,$terms); ?>
                                            <?php if(!red_is_show_canal_title()):?>
                                                <?php $new_terms=strip_tags($new_terms,'<ul><li>'); ?>
                                            <?php endif;?>
                                            <?php print $new_terms;?>
                                        </div>
				  <?php endif;?>
			</div>
		</div>
	  </div>
    
          <?php if(hontza_is_congelar_canal_sareko_id()):?>
          <div class="field field-type-text field-canal_de_yql_activated" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;"> 
				  <?php print t('Activated');?>:&nbsp;                                  
				</div>
                                <div id="id_div_activar_canal_<?php print $node->nid;?>" style="float:left;"> 
                                    <?php print hontza_canal_get_activated_string($node); ?>
                                </div>    
			</div>
		</div>
	  </div>
          <?php endif;?>
    
          <?php if($my_node->body):?>
                <div class="field field-type-text field-canal_de_yql-body" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Description');?>:&nbsp;
					</div>									
					<div class="my_div_body">
						<?php print $my_node->body;?>  
					</div>
				</div>
			</div>
		  </div>
           <?php endif;?>       
	  
	   <div class="field field-type-text field-canal_de_yql-tipo_de_fuente" style="float:left;clear:both;">
		<div class="field-items">			
			<div class="field-item odd">			
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Origin');?>:&nbsp;
				</div>
				<?php if(isset($node->field_fuente_canal[0]['view'])):?>					
					<?php print $node->field_fuente_canal[0]['view'];?>                                        
				<?php endif;?>
			</div>
		</div>
	  </div>
	  
	  <div class="field field-type-text field-canal_de_yql-responsable_uid" style="float:left;clear:both;">
		<div class="field-items">			
			<div class="field-item odd">			
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Main Validator');?>:&nbsp;
				</div>
				<?php if(isset($node->field_responsable_uid[0]['view'])):?>					
					<?php print $node->field_responsable_uid[0]['view'];?> 
				<?php endif;?>
			</div>
		</div>
	  </div>
    
          <div class="field field-type-text field-canal_de_yql-responsable_uid2" style="float:left;clear:both;">
		<div class="field-items">			
			<div class="field-item odd">			
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Second Validator');?>:&nbsp;
				</div>
				<?php if(isset($node->field_responsable_uid2[0]['view'])):?>					
					<?php print $node->field_responsable_uid2[0]['view'];?> 
				<?php endif;?>
			</div>
		</div>
	  </div>
    
	  <div class="field field-type-text field-canal_de_supercanal-nombrefuente_canal" style="float:left;clear:both;">
		<div class="field-items">			
			<div class="field-item odd">
                            <?php if(hontza_is_hound_canal($node->nid)):?>
                                    <div class="field-label-inline-first" style="float:left;">
                                      <?php print t('Hound');?>:&nbsp;
                                    </div>
                                    <?php print hontza_get_hound_title_by_nid($node->nid);?>
                            <?php else:?>
                                    <div class="field-label-inline-first" style="float:left;">
                                      <?php print t('Name of source').'/'.t('URL');?>:&nbsp;
                                    </div>
                                    <?php if(isset($node->field_nombrefuente_canal[0]['view'])):?>					
                                            <?php //gemini-2014;?>
                                            <?php //print hontza_get_enlace_fuente_del_canal($node);?>
                                            <?php print hontza_get_enlace_fuente_del_canal_view_html($node);?>
                                    <?php endif;?>
                            <?php endif;?>
			</div>
		</div>
	  </div>

          <?php if(hontza_is_hound_canal($node->nid)):?>
                <?php if(hontza_is_hound_text_input()):?>
                    <?php include('canal-hound-parametros.tpl.php');?>
                <?php else:?>
                    <div class="field field-type-text field-canal_de_yql-url_html" style="float:left;clear:both;">
                    <div class="field-items">
                            <div class="field-item odd">
                                    <div class="field-label-inline-first" style="float:left;">
                                      <?php print t('Hound search');?>:&nbsp;
                                    </div>
                                    <?php $hound_search_value=hontza_get_hound_search_value_by_nid($node->nid);?>
                                    <textarea class="form-textarea resizable textarea-processed" readonly rows="5" cols="60"><?php print urldecode($hound_search_value);?></textarea>			
                            </div>
                    </div>
                <?php endif;?>        
	  </div>    
          <?php endif;?>
    
    
    
           <div class="field field-type-text field-canal_de_yql-url_html" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Url of Html page');?>:&nbsp;
				</div>
				<?php if(isset($node->field_url_html[0]['view'])):?>
					<?php print $node->field_url_html[0]['view'];?>
				<?php endif;?>
			</div>
		</div>
	  </div>
              
            <?php //intelsat-2015 ?>
            <?php if(!hontza_solr_search_is_usuario_lector()):?>
            <div style="float:left;clear:both;">
		                            
                            <?php print traducir_average($node->content['fivestar_widget']['#value']);?>
                     
            </div>
            <?php endif;?>
          <?php if(hontza_is_sareko_id_red()):?>
                <?php include('red-canal_de_yql_fields_view.tpl.php');?>
          <?php endif;?>
          <!--table-->
          <?php endif;?>
    
          <?php print get_reto_al_que_responde_fieldset($node);?>
          
          <?php print hontza_parametros_del_canal($my_node);?>
    
          <?php if(isset($node->my_analisis) && !empty($node->my_analisis)):?>                          
            <div class="field field-type-text field-canal_de_yql-analisis" style="float:left;clear:both;">
                  <div class="field-items">			
                          <div class="field-item odd">			
                                  <div class="field-label-inline-first" style="float:left;">
                                    <?php print t('Analysis');?>:&nbsp;
                                  </div>
                                  <?php print $node->my_analisis;?>
                          </div>
                  </div>
            </div>	  	  
          <?php endif;?>
          <?php //print hontza_red_compartir_canal_link($node);?>
</div>
<!--    
<div style="float:left;clear:both;padding-top:10px;">
	<?php //print $links; ?>
</div>
-->    
<?php if(hontza_is_con_botonera()):?>	
		  <div class="n-opciones-item">
                                <?php $compartir_canal_link=hontza_red_compartir_canal_link($node);?>
                                
                                <?php if(!empty($compartir_canal_link)):?> 
                                    <div class="item-compartir-fuente">
                                        <?php print $compartir_canal_link;?>
                                    </div>
                                <?php endif;?>  
                      
                                <?php //intelsat-2015 ?>
                                <?php if(!hontza_solr_search_is_usuario_lector()):?>
                                <div class="item-editar" style="margin-top:2px;">
					 				<?php print red_canal_link_export_canal($node->nid,1);?>
                                </div>
                                <div class="item-editar">
					 				<?php print hontza_canal_edit_link($node);?>
                                </div>
                                   
                                <?php //if(is_super_admin()):?>
                                <?php if(red_canal_is_actions_canal_access($node)):?>
                                    <div class="item-canal-noticias">
                                        <?php print hontza_canal_rss_canal_noticias_link($node);?>
                                    </div>
                                    <div class="item-canal-import">
                                        <?php print hontza_canal_rss_canal_import_link($node);?>
                                    </div>
                                    <div class="item-canal-export-rss" style="display:none;">
                                        <?php print hontza_canal_rss_get_canales_rss_link($node);?>
                                    </div>
                                    <div class="item-canal-borrar-noticias">
                                        <?php print hontza_canal_rss_canal_borrar_noticias_link($node);?>
                                    </div>
                                <?php endif;?>
                                 
                      
	  			<div class="item-borrar">
                                    <?php print hontza_canal_delete_link($node);?>
	  			</div>
                                <?php endif;?>  
                </div><!-- opciones-item-->
<?php endif;?>      
</div>
<?php endif;?>	  