<?php
// $Id$

/**
 * @file
 * Outputs contents of nodes
 *
 * @see template_preprocess_node(), preprocess/preprocess-node.inc
 * http://api.drupal.org/api/function/template_preprocess_node/6
 */
?>
<div<?php print $node_attributes; ?>>
    <div class="meta">
      <?php if ($submitted): ?>
      <?php print $picture; ?>
      <?php endif; ?>
	  <?php if (!$page && $title): ?>
        <!--gemini-->
		<!--
		<h2><a href="<?php //print $node_url; ?>" title="<?php //print $title; ?>"><?php //print $title; ?></a></h2>
        -->
		<h2><?php print l($title,'node/'.$node->nid,array('attributes'=>array('title'=>$title))); ?></h2>         
	  <?php endif; ?>
      <?php if ($submitted): ?>
        <p><?php print $submitted; ?></p>
      <?php endif; ?>
      <?php if ($terms): ?>
        <div class="terms">
          <span class="icon">&nbsp;</span>
          <?php print $terms; ?>
        </div>
      <?php endif; ?>
    </div>
  <div class="content clearfix">
    <?php print $content; ?>
  </div>
  <?php if ($links): ?>
    <div class="links">
      <?php print $links; ?>
    </div>
  <?php endif; ?>
</div>