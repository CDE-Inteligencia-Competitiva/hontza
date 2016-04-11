<div class="field field-type-text field-supercanal-red-idiomas" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Languages');?>:&nbsp;
				</div>
				<?php print red_idiomas($node,'field_igape_idiomas_fuente');?>
			</div>
		</div>
</div>
<div class="field field-type-text field-supercanal-red-periodicidad" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Update frequency');?>:&nbsp;
				</div>
				<?php print red_get_periodo_view($node,'field_igape_periodo_fuente');?>
			</div>
		</div>
</div>
<div class="field field-type-text field-supercanal-red-idioma-principal" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Main Language');?>:&nbsp;
				</div>
				<?php print red_field($node,'field_igape_idioma_prin_fuente');?>
			</div>
		</div>
</div>
<div class="field field-type-text field-supercanal-red-url" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('URL Address');?>:&nbsp;
				</div>
				<?php print red_field($node,'field_igape_url');?>
			</div>
		</div>
</div>
<div class="field field-type-text field-supercanal-red-country" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Country or Region');?>:&nbsp;
				</div>
				<?php print red_field($node,'field_igape_country_fuente');?>
			</div>
		</div>
</div>