<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Technology Accessibility');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print my_create_stars_view($node->eval_accesibilidad,2,'eval_accesibilidad');?></td>       
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Complexity/Risk');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print my_create_stars_view($node->eval_riesgo_complejidad,2,'eval_riesgo_complejidad');?></td>
</tr>
<tr class="tr_node_view">   												
    <td class="td_label_node_view"><b><?php print t('Investments Level');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print my_create_stars_view($node->eval_inversiones,2,'eval_inversiones');?></td>
</tr>
<tr class="tr_node_view">   												
    <td class="td_label_node_view"><b><?php print t('Market Potential');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print my_create_stars_view($node->eval_potencial_mercado,2,'eval_potencial_mercado');?></td>
</tr>							
<tr class="tr_node_view">   												
    <td class="td_label_node_view"><b><?php print t('Strategic Impact');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print my_create_stars_view($node->eval_impacto_negocio,2,'eval_impacto_negocio');?></td>
</tr>							
<tr class="tr_node_view">   												
    <td class="td_label_node_view"><b><?php print t('Execution Delay');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print my_create_stars_view($node->eval_rapidez_de_ejecucion,2,'eval_rapidez_de_ejecucion');?></td>
</tr>							
<tr class="tr_node_view">   												
    <td class="td_label_node_view"><b><?php print t('Total score');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print my_create_stars_view($node->puntuacion_total,0);?></td>
</tr>