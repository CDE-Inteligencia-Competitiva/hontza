<table class="table_node_view" style="clear:both;">    															
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Creation Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date('d/m/Y H:i',$node->created);?></td>
    </tr>          
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Full Text');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->content['body']['#value'];?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Rating');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print oportunidad_set_empty_label_stars($node->my_stars);?></td>
    </tr>    						
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Total score');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->suma_votos;?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Subgroup');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><div class="item-teaser-texto"><?php print get_oportunidad_subgrupo_string_list($node); ?></div></td>
    </tr>   						                                                
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Guests');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><div class="item-teaser-texto"><?php print get_idea_invitados_string_list($node); ?></div></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Supported by');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><div class="item-teaser-texto" id="id_oportunidad_supported_by_<?php print $node->nid;?>"><?php print get_oportunidad_adheridas_string_list($node); ?></div></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Thematic Categories');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <?php if (count($node->categorias_tematicas)>0): ?>
                <div class="terms terms-inline" style="margin-top:0px;"><?php print $node->categorias_tematicas_html ?></div>
            <?php else:?>
                <div class="terms terms-inline" style="margin-top:0px;"><?php print t('Undefined category'); ?></div>
            <?php endif;?>
        </td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Benefits that are achieved');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->beneficios_riesgos;?></td>
    </tr>                                                             
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Evaluation of the opportunity');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print my_create_stars_view($node->eval_oportunidad,2,'eval_oportunidad');?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Application to Bussiness');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->parte_del_negocio;?></td>
    </tr>    
    <tr class="tr_node_view">
        <td colspan="2">
            <?php print get_reto_al_que_responde_fieldset($node)?>
        </td>
    </tr>
    <tr class="tr_node_view">
        <td colspan="2">
            <?php print oportunidad_list_camino($node)?>
        </td>
    </tr>     					
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Control Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date('d/m/Y',my_mktime($node->plazo_del_reto));?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Attachments');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print my_get_node_files($node);?></td>
    </tr>
</table>