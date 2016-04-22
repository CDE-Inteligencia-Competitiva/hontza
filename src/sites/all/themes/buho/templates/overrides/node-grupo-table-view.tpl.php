<table class="table_node_view" style="clear:both;">
    <?php if(!red_is_show_source_title()):?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('User');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $my_user_info['username'];?></td>
    </tr>
    <?php endif;?>        
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Creation Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date('d-m-Y H:i',$node->created);?></td>
    </tr>    
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Summary');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->og_description;?></td>
    </tr>
    <!--
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php //print t('Subject Area');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php //print $node->field_tematica[0]['value'];?></td>
    </tr>
    -->
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Type of group');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_grupos_mi_grupo_get_type_of_group_string($node);?></td>
    </tr>      							
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Group language');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_grupos_mi_grupo_get_group_language_string($node);?></td>
    </tr>     				
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Creator');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_get_username($node->uid); ?></td>
    </tr>      				
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Editor in chief');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_grupos_get_chief_editor_username($node);?></td>
    </tr>     				
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Administrators');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_grupos_get_administrators_html($node);?></td>
    </tr>
    <?php if(red_funciones_is_administrador_grupo()):?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Alchemy Api Key');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->field_alchemy_key[0]['value']; ?></td>
    </tr>  
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Open Calais Api Key');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->field_opencalais_key[0]['value']; ?></td>
    </tr>    
    <?php //intelsat-2016?>
    <!--
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php //print 'Kimonolabs Api Key';?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php //print $node->field_grupo_kimonolabs_api_key[0]['value']; ?></td>
    </tr>
    -->
    <?php endif;?>    				
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Active tabs');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_grupos_get_active_tabs_html($node);?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Time delay to delete rejected news');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_grupos_mi_grupo_get_field_delete_rejected_news_time_html($node); ?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Time delay to delete unread news');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_grupos_mi_grupo_get_field_delete_unread_news_time_html($node); ?></td>
    </tr>            
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Activate channels');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_grupos_get_activate_channels_html($node); ?></td>
    </tr>    				
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Network connected');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_grupos_get_network_connected_html($node); ?></td>
    </tr>                            
</table>