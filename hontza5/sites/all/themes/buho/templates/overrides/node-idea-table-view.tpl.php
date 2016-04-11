<table class="table_node_view" style="clear:both;">    															
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Total score');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->suma_votos;?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Creation Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date('d-m-Y H:i',$node->created);?></td>
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
        <td class="td_label_node_view"><b><?php print t('Full Text');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->content['body']['#value'];?></td>
    </tr>     
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Tags');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <div class="item-categorias"<?php print hontza_item_categorias_style(1);?>>
                <?php print hontza_todas_etiquetas_html($node);?>
            </div>  
        </td>
    </tr>
    <tr class="tr_node_view">
        <td colspan="2">
            <?php print get_reto_al_que_responde_fieldset($node)?>
        </td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Control Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date('d-m-Y',my_mktime($node->plazo_del_reto)); ?></td>
    </tr>    						                                                
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Guests');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><div class="item-teaser-texto"><?php print get_idea_invitados_string_list($node); ?></div></td>
    </tr>                
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Supported by');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><div class="item-teaser-texto" id="id_supported_by_<?php print $node->nid;?>"><?php print get_idea_adheridas_string_list($node); ?></div></td>
    </tr>        						                                                
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Subgroup');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><div class="item-teaser-texto"><?php print get_idea_subgrupo_string_list($node);?></div></td>
    </tr>        									
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Attachments');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print my_get_node_files($node);?></td>
    </tr>        
</table>
<div class="field field-type-text field-field-idea-enlaces" style="float:left;clear:both;">  
    <?php $links_content=hontza_get_enlaces_view_html($node,0,1);?>
    <?php if(!empty($links_content)):?> 
        <h3><?php print t('Links')?></h3> 
        <?php print $links_content;?>
    <?php endif;?>
</div>