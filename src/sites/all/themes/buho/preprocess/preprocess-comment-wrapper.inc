<?php
// $Id$

/**
 * @file
 * Passes variables to comment wrapper template file.
 *
 * @see template_preprocess_comment_wrapper(), templates/overrides/comment-wrapper.tpl.php
 * http://api.drupal.org/api/function/template_preprocess_comment_wrapper/6
 *
 * @return $vars
 */

$node = $vars['node'];

// Prepare the variables that will store the attributes while being processed
$vars['comment_wrapper_attributes'] = array();

// Create an ID attribute for the comment wrapper.
$vars['comment_wrapper_attributes']['id'] = 'comments';
// Create an class attribute for the comment wrapper.
$vars['comment_wrapper_attributes']['id'] = 'comment-wrapper';
// Add a class indicating no comments have been posted.
$vars['comment_wrapper_attributes']['class'][] = empty($vars['content']) ? 'no-comments' : '';
// Add support for the Skinr module.
$vars['comment_wrapper_attributes']['class'][] = $vars['skinr'];
// Add a clearfix class.
$vars['comment_wrapper_attributes']['class'][] = 'clearfix';

// Add a title, and comment count to non-forum node types.
if ($node->type != 'forum') {
  // Create a title to pass to the template file
  $vars['title'] = t('Comments');

  // Create a comment count to pass to the template file
  if ($node->comment_count != 1) {
    $vars['comment_count'] = $node->comment_count .' '. t('comments posted');
  }
  else {
    $vars['comment_count'] = '1 '. t('comment posted');
  }
}

// Render the comment wrapper attributes.
$vars['comment_wrapper_attributes'] = theme('render_attributes', $vars['comment_wrapper_attributes']);