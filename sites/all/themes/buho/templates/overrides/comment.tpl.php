<?php
// $Id$

/**
 * @file
 * Output of comment content.
 *
 * @see template_preprocess_comment(), preprocess/preprocess-comment.inc
 * http://api.drupal.org/api/function/template_preprocess_comment/6
 */
?>
<?php //gemini-2013?>
<?php $is_comentario_alerta=0;?>
<?php $is_mail=0;?>
<?php if(isset($node->is_comentario_alerta) && !empty($node->is_comentario_alerta)):?>
      <?php $is_comentario_alerta=$node->is_comentario_alerta;?>
      <?php $is_mail=1;?>
<?php endif;?>
<?php $bg=''?>
<?php if(isset($node->bg) && !empty($node->bg)):?>
    <?php $bg=$node->bg;?>
<?php endif;?>
<!-- start comment.tpl.php -->
<?php if(!$is_comentario_alerta):?>  
<div<?php print $comment_attributes; ?>>
<?php else:?>
<div<?php print $comment_attributes; ?> style="border:none;">
<?php endif;?>    
<?php if ($title): ?>
  <div class="inner">
    <span class="title"> <?php //print $title; ?>
    <?php if ($comment->new): ?>
      <span class="new"><?php print $new; ?></span>
    <?php endif; ?>
    </span>
  <?php endif; ?>
  <div style="clear:both;">
  <?php $img=my_get_user_img_src('',$comment->picture,'',$comment->uid,$is_mail,$bg);?>
  <?php $img=comment_publico_comentario_user_image($img,$is_publico);?>    
  <div style="float:left;padding-left:5px;">   
  <?php print $img; ?>
  </div>
<?php if(!empty($img)):?>
  <div class="content">
<?php else:?>
  <?php if(empty($is_publico)):?>
      <div class="content" style="padding-left:0px;"> 
  <?php else:?>    
      <div class="content" style="padding-left:10px;"> 
  <?php endif;?>        
<?php endif;?>      
  	<?php //print $content; ?>
	<?php //print $comment->comment;?>
        <?php //gemini-2013?>
        <?php if($is_comentario_alerta):?>
            <p><?php print date('d-m-Y H:i',$comment->timestamp)?></p>
        <?php endif;?>
	<?php //$my_comment=_comment_load($comment->cid);?>                                    
	<?php //print alerta_resumen_comentario($my_comment->comment,$node,$is_comentario_alerta,$content);?>
        <?php print alerta_resumen_comentario($comment->comment,$node,$is_comentario_alerta,$content);?>    
  </div>
  
  <?php //gemini-2013?>    
  <?php if(!$is_comentario_alerta):?>      
    <?php //gemini?>
    <?php print my_get_comment_files($comment);?>

    <?php if ($submitted): ?>
      <div class="info">
        <?php //gemini?>
            <?php //print $picture; ?>
        <?php if($is_publico):?>
            <?php $author=publico_register_comentario_user_register($comment,$author);?>
        <?php endif;?>  
        <?php print t('Posted by !author on !date', array('!author' => $author, '!date' => $date)); ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  </div>
  </div>
<?php //gemini-2013?>    
<?php if(!$is_comentario_alerta):?>    
<?php print $links; ?>
<?php endif; ?>
</div>
 
<!-- end comment.tpl.php -->