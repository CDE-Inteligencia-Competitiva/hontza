<div class="field field-type-text field-canal_de_yql-red-idiomas" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Languages');?>:&nbsp;
				</div>
				<?php print red_idiomas($node);?>
			</div>
		</div>
</div>
<div class="field field-type-text field-canal_de_yql-red-periodicidad" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Update frequency');?>:&nbsp;
				</div>
				<?php print red_get_periodo_view($node,red_fields_inc_get_field_periodicidad_name());?>
			</div>
		</div>
</div>
<div class="field field-type-text field-canal_de_yql-red-idioma-principal" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Main Language');?>:&nbsp;
				</div>
				<?php print red_field($node,red_fields_inc_get_field_idioma_principal_name());?>
			</div>
		</div>
</div>
<div class="field field-type-text field-canal_de_yql-red-country" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Country or Region');?>:&nbsp;
				</div>
				<?php print red_field($node,red_fields_inc_get_field_country_name());?>
			</div>
		</div>
</div>