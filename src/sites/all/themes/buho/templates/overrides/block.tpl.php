<?php
// $Id$

/**
 * @file
 * Output of block content.
 *
 * @see template_preprocess_block(), preprocess/preprocess-block.inc
 * http://api.drupal.org/api/function/template_preprocess_block/6
 */
?>
<div<?php print $block_attributes; ?>>
  <?php if ($block->subject): ?>
    <h3 class="title"><?php print $block->subject; ?></h3>
  <?php endif; ?>
  <div class="content">
    <?php print $block->content; ?>
  </div>
</div>
<!-- /block.tpl.php -->