<table class="table_node_view">
<tr class="tr_node_view">
<td class="td_label_node_view">
    <b><?php print t('Organisation');?></b>:
</td>
<td class="td_value_node_view">
    <?php print $my_info['profile_empresa'];?>
</td>
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Name');?></b>:</td>
    <td class="td_value_node_view"><?php print $my_info['profile_nombre'];?></td>
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Surname');?></b>:</td>   
    <td class="td_value_node_view"><?php print $my_info['profile_apellidos'];?></td>  
</tr>                        
    <td class="td_label_node_view"><b><?php print t('Groups');?></b>:</td>
    <td class="td_value_node_view">
        <div class="item-list">
            <ul>
                <?php print $my_info['groups_li'];?>
            </ul>
	</div>
    </td>    
</table>