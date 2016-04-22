<?php $canal_hound_parametros=hound_get_canal_hound_parametros_row($node->nid);?>
<?php if(isset($canal_hound_parametros->hound_id) && !empty($canal_hound_parametros->hound_id)):?>
                <div class="field field-type-text field-canal_de_yql<?php print $param_key;?>" style="float:left;clear:both;">
                    <div class="field-items">
                            <div class="field-item odd">
                                    <div class="field-label-inline-first" style="float:left;">
                                      <?php print t('Hound Channel Id');?>:&nbsp;
                                    </div>
                                    <?php print $canal_hound_parametros->hound_id;?> 
                            </div>
                    </div>
                </div>
<?php endif;?>
<?php if(!$canal_hound_parametros->is_empty):?>
<?php if(isset($canal_hound_parametros->parametros) && !empty($canal_hound_parametros->parametros)):?>
    <?php $parametros=unserialize($canal_hound_parametros->parametros);?>
    <?php if(!empty($parametros)):?>
        <?php foreach($parametros as $param_key=>$param_row):?>
                <?php foreach($param_row as $param_name=>$param_value):?>
                <div class="field field-type-text field-canal_de_yql<?php print $param_name;?>" style="float:left;clear:both;">
                    <div class="field-items">
                            <div class="field-item odd">
                                    <div class="field-label-inline-first" style="float:left;">
                                      <?php print hound_api_param_key_label($param_name).' ('.$param_key.')';?>:&nbsp;
                                    </div>
                                    <?php if(hound_is_parametro_text_area($param_name)):?>
                                        <textarea class="form-textarea resizable textarea-processed" readonly rows="5" cols="60"><?php print $param_value;?></textarea>			
                                    <?php else:?>
                                        <input class="form-text" readonly value="<?php print $param_value;?>"/>			                                    
                                    <?php endif;?>    
                            </div>
                    </div>
                </div>                
                <?php endforeach;?>
        <?php endforeach;?>             
    <?php endif;?>            
<?php endif;?>
<?php endif;?>