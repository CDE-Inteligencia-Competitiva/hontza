<table class="table_node_view">
    <?php if(!red_is_show_canal_title()):?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('User');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $my_user_info['username'];?></td>
    </tr>
    <?php endif;?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Creation Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date( 'd-m-Y H:i',$node->created);?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Date of last download');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print get_canal_last_update_date_format($node->nid);?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Date of last update');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print get_canal_last_import_time_format($node); ?></td>
    </tr>
    <?php if(hontza_is_congelar_canal_sareko_id()):?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Activated');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <div id="id_div_activar_canal_<?php print $node->nid;?>" style="float:left;"> 
                <?php print hontza_canal_get_activated_string($node); ?>
            </div>
        </td>
    </tr>
    <?php endif;?>
    <?php if(hontza_solr_is_solr_activado()):?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Source Type');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <?php $source_array=hontza_solr_canal_source_term_array($node->nid);?>
            <?php if (!empty($source_array)): ?>
                <div class="terms terms-inline" style="margin-top:0px;margin-left:-8px;">
                    <?php print hontza_solr_set_canal_source_type_terms_ul($source_array);?>
                </div>
            <?php endif;?>
        </td>
    </tr>
    <?php endif;?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Thematic Categories');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <?php if ($terms): ?>
                <div class="terms terms-inline" style="margin-top:0px;margin-left:-8px;">
                    <?php $new_terms=hontza_set_terms_link($node,$terms); ?>
                    <?php if(!red_is_show_canal_title()):?>
                        <?php $new_terms=strip_tags($new_terms,'<ul><li>'); ?>
                    <?php endif;?>
                    <?php print $new_terms;?>
                </div>
            <?php endif;?>
        </td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Description');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <div class="my_div_body">
                <?php print $my_node->body;?>  
            </div>
        </td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Origin');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <?php //intelsat-2015-kimonolabs?>
            <?php //if(isset($node->field_fuente_canal[0]['view'])):?>					
                <?php //print $node->field_fuente_canal[0]['view'];?> 
            <?php //endif;?>
            <?php print hontza_canal_rss_canal_supercanal_origin($node);?>
        </td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Main Validator');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <?php if(isset($node->field_responsable_uid[0]['view'])):?>					
                <?php print $node->field_responsable_uid[0]['view'];?> 
            <?php endif;?>
        </td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Second Validator');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <?php if(isset($node->field_responsable_uid2[0]['view'])):?>					
                <?php print $node->field_responsable_uid2[0]['view'];?> 
            <?php endif;?>
        </td>
    </tr>
    <?php //intelsat-2016?>
    <?php if(hontza_crm_is_activado()):?>
        <tr class="tr_node_view">
            <td class="td_label_node_view"><b><?php print t('News link type');?></b>:&nbsp;</td>
            <td class="td_value_node_view">
                <?php print crm_exportar_get_news_link_type_label($node);?>
            </td>
        </tr>
    <?php endif;?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Name of source');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
             <?php $enlace=red_funciones_get_name_of_source_by_nid_fuente_canal($node);?>
                <?php if(!empty($enlace)):?>
                    <?php if(!red_is_show_canal_title()):?>
                        <?php $enlace=strip_tags($enlace); ?>
                    <?php endif;?>
                    <?php print $enlace;?>
                <?php else:?>    
                    <?php if(isset($node->field_nombrefuente_canal[0]['view'])):?>					
                        <?php //print $node->field_nombrefuente_canal[0]['view'];?>
                        <?php $enlace=hontza_get_enlace_fuente_del_canal($node);?>
                        <?php if(!red_is_show_canal_title()):?>
                            <?php $enlace=strip_tags($enlace); ?>
                        <?php endif;?>
                        <?php print $enlace;?>
                    <?php endif;?>
                <?php endif;?>   
        </td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Source URL');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_get_enlace_fuente_del_canal_view_html($node);?></td>
    </tr>
    <?php //if(!hontza_solr_search_is_usuario_lector()):?>
    <!--
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php //print t('Average');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <div style="float:left;clear:both;">    
                <?php //print hontza_canal_rss_unset_average_label($node->content['fivestar_widget']['#value']);?>
            </div>
        </td>
    </tr>
    -->
    <?php //endif;?>
    <?php //if(hontza_is_sareko_id_red()):?>
        <?php //include('red-canal_de_yql_fields_view_table.tpl.php');?>
    <?php //endif;?>
</table>