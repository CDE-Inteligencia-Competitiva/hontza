<table class="table_node_view" style="clear:both;">
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Creation Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date('d-m-Y H:i',$node->created);?></td>
    </tr>    
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Name of admin');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->field_registrar_name_admin[0]['value'];?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Email of admin');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->field_registrar_mail_admin[0]['value'];?></td>
    </tr>
    <!--
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php //print t('Languages');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php //print red_servidor_registrar_get_languages_view_html($node);?></td>
    </tr>
    -->
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Organisation');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->field_registrar_organisation[0]['value'];?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Number of employees');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->field_registrar_empresa_tamano[0]['value'];?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Country');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_servidor_registrar_get_country_view_html($node);?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Town');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->field_registrar_empresa_ciudad[0]['value'];?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Website');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_servidor_registrar_get_website_link($node);?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Name of platform');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->field_registrar_name_platform[0]['value'];?></td>
    </tr>    
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Url address of platform');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_servidor_registrar_get_website_link($node,'url_platform');?></td>
    </tr>    
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Spanish Tags');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_servidor_registrar_get_tags_html($node);?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('English Tags');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_servidor_registrar_get_tags_html($node,'en');?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Base root');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_servidor_registrar_get_website_link($node,'base_root_local');?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Base url');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_servidor_registrar_get_website_link($node,'base_url_local');?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Logo');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_servidor_registrar_get_logo_html($node);?></td>
    </tr>
</table>
<div>
<?php print l(t('Return'),'red_servidor_registrar/registrados');?>
</div>