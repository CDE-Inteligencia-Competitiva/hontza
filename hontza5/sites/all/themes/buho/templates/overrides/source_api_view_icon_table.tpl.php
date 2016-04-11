<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Apply Alchemy');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print my_get_source_view_api_icon($node,'alchemy')?></td>
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Apply OpenCalais');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print my_get_source_view_api_icon($node,'opencalais');?></td>
</tr>
<?php //intelsat-2016?>
<?php if(!in_array($node->type,array('canal_de_supercanal','canal_de_yql'))):?>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Apply FulltextRSSFeed');?></b>:&nbsp;</td>
    <td class="td_value_node_view"><?php print my_get_source_view_api_icon($node,'full_text_rss')?></td>
</tr>
<?php endif;?>