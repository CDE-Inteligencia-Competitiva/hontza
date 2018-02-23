<?php
// $Id$

/**
 * @file
 * Template file for forum node commennts
 *
 * @see template_preprocess_comment(), preprocess/preprocess-comment.inc
 * http://api.drupal.org/api/function/template_preprocess_comment/6
 */
?>
<div<?php print $comment_attributes; ?>>
  <h2 class="title"><?php print $title ?><?php print $comment->new ? ' <a name="new" id="new">'. $new .'</a>' : '' ?></h2>
  <div class="forum-wrapper-left">
    <ul class="meta-author">
      <?php if ($picture): // print users picture, if enabled ?>
        <li class="user-picture"><?php print $picture; ?></li>
      <?php endif; ?>
      <li class="user-name"><span><?php print $name; ?></span></li>
      <?php if ($joined): ?>
        <li class="user-joined"><label>Joined:</label><span><?php print $joined; ?></span></li>
      <?php endif; ?>
    </ul>
  </div>
  <div class="forum-wrapper-right<?php print $picture ? ' with-picture' : ' without-picture'; ?>">
    <ul class="meta-post">
      <li class="date"><label>Posted:</label><span><?php print $date; ?></span></li>
      <li class="comment-link"><span class="icon"><?php print $comment_link; ?></span></li>
    </ul>
    <div class="content">
      <div class="inner">
        <?php print $content; ?>
      </div>
    </div>
  </div>
  <?php if ($links): ?>
    <div class="links clearfix">
      <?php print $links; ?>
    </div>
  <?php endif; ?>
</div>