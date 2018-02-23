<fieldset class="proyecto_view_fieldset">
		  	<legend><?php print t('Rating');?></legend>
			<div class="fieldset-wrapper">
				<div class="field field-type-text field-field-proyecto-eval_accesibilidad div_eval">
					<div class="field-items">
						<div class="field-item odd">
							<div class="field-label-inline-first" style="float:left;">
							  <?php print t('Technology Accessibility');?>:&nbsp;
							</div>
							<?php //print $node->eval_accesibilidad;?>
							<?php print my_create_stars_view($node->eval_accesibilidad,2,'eval_accesibilidad');?>
						</div>
					</div>
		  		</div>



                                <div class="field field-type-text field-field-proyecto-eval_riesgo_complejidad div_eval">
					<div class="field-items">
						<div class="field-item odd">
							<div class="field-label-inline-first" style="float:left;">
							  <?php print t('Complexity/Risk');?>:&nbsp;
							</div>
							<?php //print $node->eval_accesibilidad;?>
							<?php print my_create_stars_view($node->eval_riesgo_complejidad,2,'eval_riesgo_complejidad');?>
						</div>
					</div>
		  		</div>


				<div class="field field-type-text field-field-proyecto-eval_inversiones" div_eval>
					<div class="field-items">
						<div class="field-item odd">
							<div class="field-label-inline-first" style="float:left;">
							  <?php print t('Investments Level');?>:&nbsp;
							</div>
							<?php //print $node->eval_inversiones;?>
							<?php print my_create_stars_view($node->eval_inversiones,2,'eval_inversiones');?>
						</div>
					</div>
		  		</div>



				<div class="field field-type-text field-field-proyecto-eval_potencial_mercado" div_eval>
					<div class="field-items">
						<div class="field-item odd">
							<div class="field-label-inline-first" style="float:left;">
							  <?php print t('Market Potential');?>:&nbsp;
							</div>
							<?php //print $node->eval_potencial_mercado;?>
							<?php print my_create_stars_view($node->eval_potencial_mercado,2,'eval_potencial_mercado');?>
						</div>
					</div>
		  		</div>



				<div class="field field-type-text field-field-proyecto-eval_impacto_negocio" div_eval>
					<div class="field-items">
						<div class="field-item odd">
							<div class="field-label-inline-first" style="float:left;">
							  <?php print t('Strategic Impact');?>:&nbsp;
							</div>
							<?php //print $node->eval_impacto_negocio;?>
							<?php print my_create_stars_view($node->eval_impacto_negocio,2,'eval_impacto_negocio');?>
						</div>
					</div>
		  		</div>



                                <div class="field field-type-text field-field-proyecto-eval_rapidez_de_ejecucion" div_eval>
					<div class="field-items">
						<div class="field-item odd">
							<div class="field-label-inline-first" style="float:left;">
							  <?php print t('Execution Delay');?>:&nbsp;
							</div>
							<?php //print $node->eval_rapidez_de_ejecucion;?>
							<?php print my_create_stars_view($node->eval_rapidez_de_ejecucion,2,'eval_rapidez_de_ejecucion');?>
						</div>
					</div>
		  		</div>

                                <div class="field field-type-text field-field-proyecto-puntuacion_total" div_eval>
					<div class="field-items">
						<div class="field-item odd">
							<div class="field-label-inline-first" style="float:left;">
							  <?php print t('Total score');?>:&nbsp;
							</div>
							<?php //print $node->eval_rapidez_de_ejecucion;?>
							<?php print my_create_stars_view($node->puntuacion_total,0);?>
						</div>
					</div>
		  		</div>
			</div> <!--<div class="fieldset-wrapper">-->
		 </fieldset>
