<?php if(red_despacho_is_canal_fuente_titulo_activado()):?>
	<?php $num=count($node->field_item_source_url);?>
	<?php if($num>0):?>
        <!--
        <div class="field field-type-text field-item-source-title-url" style="float:left;clear:both;">
                            <div class="field-items">
                                    <div class="field-item odd">
                                            <div class="field-label-inline-first" style="float:left;">
                                              <?php //print t('Source Title').$kont;?>:&nbsp;
                                            </div>
                                            <?php //print red_despacho_decode_source_title_url_value($node->field_item_source_title[$i]['value']);?>
                                    </div>
                            </div>
            </div>


            <div class="field field-type-text field-item-source-title-url" style="float:left;clear:both;">
                            <div class="field-items">
                                    <div class="field-item odd">
                                            <div class="field-label-inline-first" style="float:left;">
                                              <?php //print t('Source Url').$kont;?>:&nbsp;
                                            </div>
                                             <?php //print red_despacho_decode_source_title_url_value($row['value'],1);?>
                                    </div>
                            </div>
            </div>
            -->

		<?php foreach($node->field_item_source_url as $i=>$row):?>
			<?php $kont=red_despacho_get_field_kont_form_alter($i,$num);?>
			
                            <!--
                            <div style="float:left;">
                                            <span class="etiqueta-gris"><?php //print t('Source').$kont;?>: </span>
                                            <?php //print red_despacho_get_source_link($node->field_item_source_title[$i]['value'],$row['value']);?>
                                    
                            </div>
                            -->
                            

                             <div class="field field-type-text field-item-source-title-url" style="float:left;">
                            <div class="field-items">
                                    <div class="field-item odd">
                                            <div class="field-label-inline-first" style="float:left;">
                                              <?php print t('Source').$kont;?>:&nbsp;
                                            </div>
                                             <?php print red_despacho_get_source_link($node->field_item_source_title[$i]['value'],$row['value']);?>
                                    </div>
                            </div>
                            </div>
        	

		<?php endforeach;?>
	<?php endif;?>		
<?php endif;?>	











