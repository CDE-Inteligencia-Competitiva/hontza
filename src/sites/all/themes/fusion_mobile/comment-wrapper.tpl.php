<?php
// $Id$

/**
 * @file
 * Outputs the wrapper markup for comments.
 *
 * @see template_preprocess_comment_wrapper(), preprocess/preprocess-comment-wrapper.inc
 * http://api.drupal.org/api/function/template_preprocess_comment_wrapper/6
 */
?>
<?php //gemini-2013?>
<?$is_comentario_alerta=0;?>
<?php if(isset($node->is_comentario_alerta) && !empty($node->is_comentario_alerta)):?>
      <?php $is_comentario_alerta=$node->is_comentario_alerta;?>
<?php endif;?>
<?php if(!$is_comentario_alerta):?>
<div<?php print $comment_wrapper_attributes; ?>>
<?php else:?>
<div<?php print $comment_wrapper_attributes; ?> style="margin:0;">    
<?php endif;?>    
  <?php if(!$is_comentario_alerta):?>  
    <?php if ($title): ?>
      <h3><?php print $title; ?></h3>
    <?php endif; ?>
    <?php print $comment_count; ?>
  <?php endif; ?> 
  <?php print $content; ?>
</div> 