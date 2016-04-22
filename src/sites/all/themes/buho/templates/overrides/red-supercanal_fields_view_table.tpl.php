<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Languages');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_idiomas($node,'field_igape_idiomas_fuente');?></td>
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Update frequency');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_get_periodo_view($node,'field_igape_periodo_fuente');?></td>
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Main Language');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_field($node,'field_igape_idioma_prin_fuente');?></td>
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('URL Address');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_field($node,'field_igape_url');?></td>
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Country or Region');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_field($node,'field_igape_country_fuente');?></td>
</tr>