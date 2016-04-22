<?php $my_node=node_load($node->nid)?>
<table class="table_node_view">
    <?php if(!red_is_show_source_title()):?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('User');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $my_user_info['username'];?></td>
    </tr>
    <?php endif;?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Creation Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date('d-m-Y H:i',$node->created); ?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Type');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <?php if ($terms): ?>
                <?php $terms=translate_html_terms($terms);?>
                <?php if(!red_is_show_source_title()):?>
                    <?php $terms=strip_tags($terms,'<ul><li>'); ?>
                <?php endif;?>
		<div class="terms terms-inline" style="margin-top:0px;margin-left:-8px;"><?php print $terms ?></div>
            <?php endif;?>
        </td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Summary');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <div class="my_div_body">
                <?php print $my_node->body;?>  
            </div>
        </td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Source');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <?php if(count($node->field_supercanal_fuente>0)):?>
                <?php foreach($node->field_supercanal_fuente as $i=>$fuente):?>
                    <?php print $fuente['value'];?>&nbsp; 
		<?php endforeach;?>
            <?php endif;?>
        </td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Parameter');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <div style="float:left;">
                <ul class="my_supercanal_ul">
                    <?php if(count($node->field_supercanal_args>0)):?>
                        <?php foreach($node->field_supercanal_args as $i=>$my_args):?>
                            <li class="my_supercanal_li"><?php print $my_args['value'];?>&nbsp;</li> 
			<?php endforeach;?>
                    <?php endif;?>
		</ul>
            </div>   
        </td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Prompt');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <div style="float:left;">
                <ul class="my_supercanal_ul">
                    <?php if(count($node->field_supercanal_args_desc>0)):?>
                        <?php foreach($node->field_supercanal_args_desc as $i=>$my_args_desc):?>
                            <li class="my_supercanal_li"><?php print $my_args_desc['value'];?>&nbsp;</li>  
			<?php endforeach;?>
                    <?php endif;?>
		</ul>
            </div>
        </td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Quality');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_get_fuente_stars_view($node,'field_supercanal_calidad');?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Coverage');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_get_fuente_stars_view($node,'field_supercanal_exhaustividad');?></td>
    </tr>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Update');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print hontza_get_fuente_stars_view($node,'field_supercanal_actualizacion');?></td>
    </tr>
    <?php include('source_api_view_icon_table.tpl.php');?>
    <?php if(hontza_is_sareko_id_red()):?>
        <?php include('red-supercanal_fields_view_table.tpl.php');?>
    <?php endif;?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Type of Group');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <?php if(count($node->field_tematica_gupos)>0):?>
                <?php foreach($node->field_tematica_gupos as $i=>$gu):?>
                    <?php print $gu['value'];?>&nbsp; 
		<?php endforeach;?>
            <?php endif;?>
        </td>
    </tr>    
</table>