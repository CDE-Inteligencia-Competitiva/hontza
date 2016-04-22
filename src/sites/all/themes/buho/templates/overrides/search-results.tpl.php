<?php

/**
 * @file search-results.tpl.php
 * Default theme implementation for displaying search results.
 *
 * This template collects each invocation of theme_search_result(). This and
 * the child template are dependant to one another sharing the markup for
 * definition lists.
 *
 * Note that modules may implement their own search type and theme function
 * completely bypassing this template.
 *
 * Available variables:
 * - $search_results: All results as it is rendered through
 *   search-result.tpl.php
 * - $type: The type of search, e.g., "node" or "user".
 *
 *
 * @see template_preprocess_search_results()
 */
?>
<?php if(!empty($solr_save_search_form)):?>
<div id="block-stored-views-save" class="block block-stored-views block-even region-odd clearfix ">
    <div class="content">
        <?php print $solr_save_search_form;?>
    </div>
</div>
<?php endif;?>
<!--
<div style="padding-bottom:5px;padding-top:5px;">
-->

<div class="div_solr_result_message" style="padding-top:5px;float:left;">
    <div style="float:left;padding-left:5px;">
    <b><?php print hontza_solr_funciones_get_result_message($my_num_found_solr,$my_start_solr,$my_end_solr);?></b>
    </div>    
    <div style="float:left;padding-left:10px;">
    <?php if(!empty($solr_my_buttons)):?>
        <?php print $solr_my_buttons;?>
    <?php endif;?>
    </div>    
</div>

<div style="clear:both;">
<?php print hontza_solr_funciones_get_en_grupo_ini();?>
</div>
<?php print $search_results; ?>

<?php print $pager; ?>
