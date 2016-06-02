<?php $canal_hound_parametros=hound_get_canal_hound_parametros_row($node->nid);?>
<?php if(!hound_enlazar_inc_is_activado()):?>
<?php if(isset($canal_hound_parametros->hound_id) && !empty($canal_hound_parametros->hound_id)):?>
        <tr class="tr_node_view">
            <td class="td_label_node_view"><b><?php print t('Hound Channel Id');?></b>:&nbsp;</td>
            <td class="td_value_node_view"><?php print $canal_hound_parametros->hound_id;?></td>
        </tr>    
<?php endif;?>
<?php endif;?>        
<?php if(!$canal_hound_parametros->is_empty):?>
<?php if(hound_enlazar_inc_is_activado()):?>
     <!--
     <table>
     -->
     <!--    
     <tr>
     <th><?php //print t('Hound');?></th>
     <th><?php //print t('Parameter');?></th> 
     <th><?php //print t('Value');?></th>  
     </tr>
     -->
    <?php $parametros=json_decode(base64_decode($canal_hound_parametros->content));?>
    <?php if(!empty($parametros)):?>
        <?php foreach($parametros as $param_key=>$hound_array):?>
            <?php if(empty($hound_array)):?>
                 <tr class="tr_node_view" style="background-color:#CCCCCC">
            
                 <!--
                 <td class="td_label_node_view"><b><?php //print $param_key;?></b></td>
                 <td></td>
                 <td></td>
                 -->
                 <td class="td_label_node_view"><b><?php print t('Hound')?>:</b></td>
                 <td><?php print $param_key;?></td>
                 </tr>
                 <!--
                 <tr class="tr_node_view">
                     <td colspan="2"><?php //print 'No parameters';?><td>
                 </tr>
                 -->
            <?php else:?>
                 <?php foreach($hound_array as $i=>$param_row):?>
                    <tr class="tr_node_view" style="background-color:#CCCCCC">
                        <td class="td_label_node_view"><b><?php print t('Hound')?>:</b></td>
                        <td><?php print $param_key;?></td>
                    </tr>
                     <?php foreach($param_row as $param_name=>$param_value):?>
                            <tr class="tr_node_view">
                            <!--    
                            <td class="td_label_node_view"><b><?php //print $param_key;?></b></td>
                            -->
                            <td class="td_label_node_view"><b><?php print hound_api_param_key_label($param_name);?></b></td>                                                                         
                                <?php if(hound_is_parametro_text_area($param_name)):?>
                                <td class="td_value_node_view"><textarea class="form-textarea resizable textarea-processed" readonly rows="5" cols="60"><?php print $param_value;?></textarea></td>	
                                <?php else:?>
                                    <td class="td_value_node_view"><input class="form-text" readonly value="<?php print $param_value;?>"/></td>			                                    
                                <?php endif;?>
                             </tr>             
                  <?php endforeach;?>       
                  <?php endforeach;?>                              
            <?php endif;?>
                
        <?php endforeach;?>
    <?php endif;?>
    <!--                         
    </table>
    -->
<?php else:?>        
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
<?php endif;?>