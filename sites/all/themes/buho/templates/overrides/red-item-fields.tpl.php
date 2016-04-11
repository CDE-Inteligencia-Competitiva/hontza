<div class="field field-type-text field-item-red-resumen" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Summary');?>:&nbsp;
				</div>
				<?php print red_get_summary_view_html($node);?>
			</div>
		</div>
</div>
<?php $fecha_noticia=red_get_fecha_noticia_view_html($node);?>
<?php if(!empty($fecha_noticia)):?>
<div class="field field-type-text field-item-red-fecha-noticia" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('News Date');?>:&nbsp;
				</div>
				<?php print $fecha_noticia;?>
			</div>
		</div>
</div>
<?php endif;?>
<?php //if($node->type=='item'):?>
<div class="field field-type-text field-item-tipo-noticia" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('News Type');?>:&nbsp;
				</div>
				<?php print red_get_item_tipo_noticia_html($node);?>
			</div>
		</div>
</div>
<div class="field field-type-text field-item-unidades" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Units');?>:&nbsp;
				</div>
				<?php print red_get_item_unidades_html($node);?>
			</div>
		</div>
</div>
<div class="field field-type-text field-item-direccion-url" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('URL Address');?>:&nbsp;
				</div>
				<?php print red_field($node,red_fields_inc_get_field_item_web_name());?>
			</div>
		</div>
</div>
<div class="field field-type-text field-item-boletines" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Bulletins');?>:&nbsp;
				</div>
				<?php print red_boletines($node);?>
			</div>
		</div>
</div>
<div class="field field-type-text field-item-fuente-de-la-noticia" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('News source');?>:&nbsp;
				</div>
				<?php print red_field($node,red_fields_inc_get_field_red_fuente_noticia_name());?>
			</div>
		</div>
</div>
<div class="field field-type-text field-item-sectorizacion" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Sectorisation');?>:&nbsp;
				</div>
				<?php print red_sectorizacion($node);?>
			</div>
		</div>
</div>
<div class="field field-type-text field-item-cnae" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print 'CNAE';?>:&nbsp;
				</div>
				<?php print red_cnae($node);?>
			</div>
		</div>
</div>
<?php //endif;?>