<table class="table_node_view" style="clear:both;">
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('User');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $my_user_info['username'];?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Creation Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date('d-m-Y H:i',$node->created);?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Who');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->field_quien[0]['value'];?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Where');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->field_donde[0]['value'];?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('When');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date('d-m-Y',$node->field_cuando[0]['value']);?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Channel');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_get_canal_usuarios_link($node->uid);?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Tags');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_todas_etiquetas_html($node);?></td>
    </tr>    
    <?php if(red_despacho_is_noticia_usuario_source_type_show($node)):?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Source Type');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <div style="margin-top:0px;float:left;" class="terms terms-inline">
                <?php print hontza_solr_funciones_get_item_source_types($node,1);?>
            </div>
        </td>
    </tr>
    <?php endif;?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Thematic Categories');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <div style="margin-top:0px;float:left;" class="terms terms-inline">
                <?php print hontza_solr_search_get_noticia_categorias_tematicas_html($node,1);?>
            </div>
        </td>
    </tr>
    <?php //intelsat-2016?>
    <?php //if(hontza_crm_is_activado()):?>
    <!--
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php //print t('News link type');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php //print crm_exportar_get_news_link_type_label($node);?></td>
    </tr>
    -->
    <?php //endif;?>
    <?php //if(hontza_is_sareko_id_red()):?>
        <?php //include_once('red-item-fields-table-view.tpl.php');?>
    <?php //endif;?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Visits');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print red_reads_visitas($node);?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('File attachments');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print my_get_node_files($node);?></td>
    </tr>        
</table>
<?php print get_reto_al_que_responde_fieldset($node);?>