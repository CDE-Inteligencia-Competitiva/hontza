<?php

/**
 * @file user-profile-item.tpl.php
 * Default theme implementation to present profile items (values from user
 * account profile fields or modules).
 *
 * This template is used to loop through and render each field configured
 * for the user's account. It can also be the data from modules. The output is
 * grouped by categories.
 *
 * @see user-profile-category.tpl.php
 *      for the parent markup. Implemented as a definition list by default.
 * @see user-profile.tpl.php
 *      where all items and categories are collected and printed out.
 *
 * Available variables:
 * - $title: Field title for the profile item.
 * - $value: User defined value for the profile item or data from a module.
 * - $attributes: HTML attributes. Usually renders classes.
 *
 * @see template_preprocess_user_profile_item()
 */
?>
<?php if(hontza_canal_rss_is_usuario_ficha_tabla()):?>
<?php //intelsat-2015 ?>
<?php if(red_crear_usuario_is_view_user_field($title)):?>
<tr class="tr_node_view">
<td class="td_label_node_view">
    <b>
        <?php if($attributes==' class="profile-profile_es_empresa_de_servicios"'):?>            
            <?php print t('Is a services company?');?> 
        <?php else:?>
        <?php print my_translate_profile_item($title,$attributes); ?>
        <?php endif;?>
    </b>:
</td>
<td class="td_value_node_view">    
    <?php $my_value=my_translate_profile_value($value,$title,$attributes); ?>
    <?php $konp=$my_value;?>
    <?php $my_value=str_replace('<ul class="item-list">','<div class="item-list"><ul>',$my_value);?>
    <?php if($konp!=$my_value):?>
        </div>
    <?php endif;?>
    <?php print $my_value;?>
</td>    
</tr>
<?php endif;?>
<?php else:?>
<dt<?php print $attributes; ?>><?php print my_translate_profile_item($title); ?></dt>
<dd<?php print $attributes; ?>><?php print my_translate_profile_value($value,$title); ?></dd>
<?php endif;?>
