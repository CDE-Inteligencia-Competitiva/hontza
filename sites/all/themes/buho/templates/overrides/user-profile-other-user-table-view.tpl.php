<table class="table_node_view">
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Photo');?></b>:</td>
    <td class="td_value_node_view">
        <?php  if($account->picture):?> 
        <?php //print $account->picture; ?>
        <?php print my_get_user_img_src('',$account->picture,$account->name,$account->uid);?>
        <?php else:?>
              <?php print my_get_user_img_src('','',$account->name,$account->uid);?>
        <?php endif;?>        
    </td>
</tr>    
<tr class="tr_node_view">
<td class="td_label_node_view">
    <b><?php print t('Organisation');?></b>:
</td>
<td class="td_value_node_view">
    <?php print $my_info['profile_empresa'];?>
</td>
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Username');?></b>:</td>
    <td class="td_value_node_view"><?php print $account->name;?></td>
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Name');?></b>:</td>
    <td class="td_value_node_view"><?php print $my_info['profile_nombre'];?></td>
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Surname');?></b>:</td>   
    <td class="td_value_node_view"><?php print $my_info['profile_apellidos'];?></td>  
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Email');?></b>:</td>   
    <td class="td_value_node_view"><?php print $account->mail;?></td>  
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Status');?></b>:</td>   
    <td class="td_value_node_view"><?php print red_crear_usuario_get_user_status_label($account);?></td>  
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Roles');?></b>:</td>   
    <td class="td_value_node_view"><?php print hontza_get_user_roles_li($account->uid);?></td>  
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Language');?></b>:</td>   
    <td class="td_value_node_view"><?php print red_crear_usuario_get_user_language_label($account);?></td>  
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Start page');?></b>:</td>   
    <td class="td_value_node_view"><?php print red_crear_usuario_get_user_start_page_html($account);?></td>  
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Member for');?></b>:</td>   
    <td class="td_value_node_view"><?php print $account->content['summary']['member_for']['#value'];?></td>  
</tr>
<tr class="tr_node_view">
    <td class="td_label_node_view"><b><?php print t('Groups');?></b>:</td>
    <td class="td_value_node_view">
        <div class="item-list">
            <ul>
                <?php print $my_info['groups_li'];?>
            </ul>
	</div>
    </td>
</tr>    
</table>