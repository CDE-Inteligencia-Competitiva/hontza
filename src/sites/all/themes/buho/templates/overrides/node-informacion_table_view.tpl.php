<table class="table_node_view" style="clear:both;">
    <!--
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php //print t('User');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php //print $my_user_info['username'];?></td>
    </tr>
    -->
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Creation Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date('d-m-Y H:i',$node->created);?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Full Text');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->content['body']['#value'];?></td>
    </tr>    					                                        
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Importance');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print get_eval_label($node->importancia, 'importancia');?></td>
    </tr>    					
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Accessibility');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print get_eval_label($node->accesibilidad, 'accesibilidad');?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Total punctuation');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->puntuacion_total;?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Working Group');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->grupo_seguimiento_link;?></td>
    </tr>         
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Control Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print estrategia_inc_get_control_date($node);?></td>
    </tr>
    <!--
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php //print t('No Control Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php //print informacion_get_no_control_date_label($node->no_control_date); ?></td>
    </tr>
    -->
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Attachments');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print my_get_node_files($node);?></td>
    </tr>  
</table>