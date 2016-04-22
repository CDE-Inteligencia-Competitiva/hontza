<table class="table_node_view" style="clear:both;">    															
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Creation Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date('d/m/Y H:i',$node->created);?></td>
    </tr>    					
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Project Status');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->estado_del_proyecto_label; ?></td>
    </tr>   											  
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Full Text');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->content['body']['#value'];?></td>
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
        <td colspan="2">
            <?php print get_reto_al_que_responde_fieldset($node)?>
        </td>
    </tr>
    <tr class="tr_node_view">
        <td colspan="2">
            <?php print oportunidad_list_camino($node)?>
        </td>
    </tr>
    <?php include('evaluacion-proyecto-table-view.tpl.php');?>
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Acronym');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->acronimo;?></td>
    </tr>    														
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Image or logo');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <?php if(isset($node->field_imagen_o_logo[0]) && isset($node->field_imagen_o_logo[0]['view'])):?>	
                <?php print $node->field_imagen_o_logo[0]['view'];?> 
            <?php endif;?>
        </td>
    </tr>     														
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Phases');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->fases;?></td>
    </tr>     					
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Estimated duration of project');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->duracion_estimada;?></td>
    </tr>    								    
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Knowledge available');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->experiencia_disponible;?></td>
    </tr>     														
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Knowledge necessary');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->experiencia_necesaria;?></td>
    </tr>     														
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Partners involved');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->socios_involucrados;?></td>
    </tr>    														
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Partners contacted');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->socios_contactados;?></td>
    </tr>    														
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Potential partners');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print $node->socios_potenciales;?></td>
    </tr>    
    <?php print proyecto_get_numero_socios_table($node);?>        
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Control Date');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print date('d-m-Y',my_mktime($node->plazo_del_reto)); ?></td>
    </tr>    						
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Subgroup');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <div style="float:left;">
                <ul class="my_supercanal_ul">
                <?php if(count($node->personas_list)>0):?>
                    <?php foreach($node->personas_list as $i=>$p):?>
                        <li class="my_supercanal_li"><?php print $p->username;?>&nbsp;</li>
                    <?php endforeach;?>
		<?php endif;?>
		</ul>
            </div>
        </td>
    </tr>     						
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Guests');?></b>:&nbsp;</td>
        <td class="td_value_node_view">
            <div style="float:left;">
                <ul class="my_supercanal_ul">
		<?php if(count($node->invitados_list)>0):?>
                    <?php foreach($node->invitados_list as $i=>$p):?>
                        <li class="my_supercanal_li"><?php print $p->username;?>&nbsp;</li>
                    <?php endforeach;?>
		<?php endif;?>
		</ul>
            </div>
        </td>
    </tr>    															
    <tr class="tr_node_view">
        <td class="td_label_node_view"><b><?php print t('Attachments');?></b>:&nbsp;</td>
        <td class="td_value_node_view"><?php print my_get_node_files($node);?></td>
    </tr>
</table>