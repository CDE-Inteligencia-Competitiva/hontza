<tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Languages');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_idiomas($node);?></td>
</tr>
<tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Update frequency');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_get_periodo_view($node,red_fields_inc_get_field_periodicidad_name());?></td>
</tr>
<tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Main Language');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_field($node,red_fields_inc_get_field_idioma_principal_name());?></td>
</tr>
<tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Country or Region');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_field($node,red_fields_inc_get_field_country_name());?></td>
</tr>