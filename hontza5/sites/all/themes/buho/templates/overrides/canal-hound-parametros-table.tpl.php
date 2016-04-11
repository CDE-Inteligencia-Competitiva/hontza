<?php $canal_hound_parametros=hound_get_canal_hound_parametros_row($node->nid);?>
<?php if(isset($canal_hound_parametros->hound_id) && !empty($canal_hound_parametros->hound_id)):?>
        <tr class="tr_node_view">
            <td class="td_label_node_view"><b><?php print t('Hound Channel Id');?></b>:&nbsp;</td>
            <td class="td_value_node_view"><?php print $canal_hound_parametros->hound_id;?></td>
        </tr>    
<?php endif;?>
<?php if(!$canal_hound_parametros->is_empty):?>
<?php if(isset($canal_hound_parametros->parametros) && !empty($canal_hound_parametros->parametros)):?>
    <?php $parametros=unserialize($canal_hound_parametros->parametros);?>
    <?php if(!empty($parametros)):?>
        <?php foreach($parametros as $param_key=>$param_row):?>
                <?php foreach($param_row as $param_name=>$param_value):?>
                    <tr class="tr_node_view">
                        <td class="td_label_node_view"><b><?php print hound_api_param_key_label($param_name).' ('.$param_key.')';?></b></td>                                                                         
                        <?php if(hound_is_parametro_text_area($param_name)):?>
                        <td class="td_value_node_view"><textarea class="form-textarea resizable textarea-processed" readonly rows="5" cols="60"><?php print $param_value;?></textarea></td>	
                        <?php else:?>
                            <td class="td_value_node_view"><input class="form-text" readonly value="<?php print $param_value;?>"/></td>			                                    
                        <?php endif;?>    
                    </tr>             
                <?php endforeach;?>
        <?php endforeach;?>             
    <?php endif;?>            
<?php endif;?>
<?php endif;?>