<?php
// $Id$

/**
 * @file
 * Passes variables to comment template files.
 *
 * @see template_preprocess_comment(), templates/overrides/comment.tpl.php
 * http://api.drupal.org/api/function/template_preprocess_comment/6
 *
 * @return $vars
 */

$comment = $vars['comment'];
/*
//gemini
echo $comment->comment;
$comment->comment=htmlspecialchar_decode($comment->comment);
echo $comment->comment;
//
*/
// menu_get_object doesn't work here.
$node = node_load($comment->nid);

// Prepare the variables that will store the attributes while being processed
$vars['comment_attributes'] = $comment->attributes ? $comment->attributes : array();

$advanced_forum = function_exists('advanced_forum_theme');
if ($node->type == 'forum' && !$advanced_forum) {
  // User information
  $accountid = $comment->uid;

  // Add an ID
  $vars['comment_attributes']['id'] = 'forum-reply-'. $comment->cid;
  // Let the comments share some of the same classes as the node
  $vars['comment_attributes']['class'][] = 'forum-post';
  // Add a class that indicates this is a reply
  $vars['comment_attributes']['class'][] = 'forum-post-reply';
  // Add a class that indicates a new reply
  $vars['comment_attributes']['class'][] = $comment->new ? ' forum-reply-new' : '';

  if ($accountid == 0) {
    // Anonymous User
    $vars['account']->name = $comment->name;
    $vars['account']->homepage = $comment->homepage;
  }
  else {
    // Authenticated User
    $vars['account'] = user_load(array('uid' => $comment->uid));
    $vars['joined'] = t('<abbr class="created" title="!microdate">@date</abbr>', array(
    '@date' => format_date($vars['account']->created, 'custom', "m/d/y"),
    '!microdate' => format_date($vars['account']->created, 'custom', "Y-m-d\TH:i:sO"),
    ));
  }

  // provide an icon for comment copy link
  $icon_path = path_to_theme() .'/images/icn-link.png';
  $icon = theme('image', $icon_path, t('Copy comment link'), NULL, NULL, TRUE);

  // if there is no image, provide alternative text
  if (!$icon) {
    $icon = '#'. $comment->cid;
  }
  $vars['comment_link'] = l($icon, 'node/'. $comment->nid, array(
    'attributes' => array(
      'class' => 'copy-comment',
      'title' => t('Grab this comment\'s link'),
      'onclick' => 'javascript:return false;',
    ),
    'fragment' => 'comment-'. $comment->cid,
    'html' => TRUE,
    )
  );
  $vars['name'] = theme('username', $vars['account']);
}
else {
  // Add a class that indicates this is a comment.
  $vars['comment_attributes']['class'][] = 'comment';
  // Add a class that shows if a post is new or not
  $vars['comment_attributes']['class'][] = $comment->new ? ' comment-new' : '';
}

// All comments
// Add the clearfix class
$vars['comment_attributes']['class'][] = 'clearfix';
// Add support for the Skinr module.
$vars['comment_attributes']['class'][] = $vars['skinr'];

// Crunch all attributes into a single string to be added to the block container.
$vars['comment_attributes'] = theme('render_attributes', $vars['comment_attributes']);
