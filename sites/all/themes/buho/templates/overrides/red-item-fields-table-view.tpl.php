<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Summary');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_get_summary_view_html($node);?></td>    
</tr>
<?php $fecha_noticia=red_get_fecha_noticia_view_html($node);?>
<?php if(!empty($fecha_noticia)):?>
    <tr class="tr_node_view">        
        <td class="td_label_node_view"><b><?php print t('News Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $fecha_noticia;?></td>
    </tr>    
<?php endif;?>
<tr class="tr_node_view">        
    <td class="td_label_node_view"><b><?php print t('News Type');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_get_item_tipo_noticia_html($node);?></td>
</tr>
<tr class="tr_node_view">        
    <td class="td_label_node_view"><b><?php print t('Units');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_get_item_unidades_html($node);?></td>
</tr>
<tr class="tr_node_view">        
    <td class="td_label_node_view"><b><?php print t('URL Address');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_field($node,red_fields_inc_get_field_item_web_name());?></td>
</tr>
<tr class="tr_node_view">        
    <td class="td_label_node_view"><b><?php print t('Bulletins');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_boletines($node);?></td>
</tr>
<tr class="tr_node_view">        
    <td class="td_label_node_view"><b><?php print t('News source');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_field($node,red_fields_inc_get_field_red_fuente_noticia_name());?></td>
</tr>
<tr class="tr_node_view">        
    <td class="td_label_node_view"><b><?php print t('Sectorisation');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_sectorizacion($node);?></td>
</tr>
<tr class="tr_node_view">        
    <td class="td_label_node_view"><b><?php print 'CNAE';?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print red_cnae($node);?></td>
</tr>