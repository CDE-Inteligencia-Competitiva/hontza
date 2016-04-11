<?php
// $Id$
/**
 * @file
 * Passes vars to node.tpl.php.
 *
 * @see template_preprocess_block(), templates/overrides/node.tpl.php
 * http://api.drupal.org/api/function/template_preprocess_node/6
 *
 * @return $vars
 */

/**
 * Use $node as opposed to $vars['node].
 * Helps me to separate what's there vs. what I've created.
 */
$node = $vars['node'];

// Prepare the vars that will store the attributes while being processed
$vars['node_attributes'] = $node->attributes ? $node->attributes : array();

// Add an id for specific block styling.
$vars['node_attributes']['id'] = 'node-'. $node->nid;

// Add a common class to all blocks.
$vars['node_attributes']['class'][] = 'node';
// Add a clearfix class.
$vars['node_attributes']['class'][] = 'clearfix';
// Add a class to allow for styling of blocks produced by a specific module.
$vars['node_attributes']['class'][] = 'node-'. $node->type;
// Add a class to allow styling based on if a node is showing a teaser or the whole thing.
$vars['node_attributes']['class'][] = $vars['teaser'] ? 'node-teaser' : 'node-full';
// Add a class to allow styling based on publish status.
$vars['node_attributes']['class'][] = ($node->status == 1) ? 'published' : 'not-published';
// Add a class to allow styling based on promotion.
$vars['node_attributes']['class'][] = ($node->promote == 1) ? 'promoted' : 'not-promoted';
// Add a class to allow styling based on sticky status.
$vars['node_attributes']['class'][] = ($node->sticky == 1) ? 'sticky' : 'not-sticky';
// Add a class that indicates if there is author photo or not
$vars['node_attributes']['class'][] = ($vars['picture'] !== '') ? 'with-photo' : 'without-photo';
// Add support for the Skinr module.
$vars['node_attributes']['class'][] = $vars['skinr'];

// Remove classes on links, the wrapper classes are enough.
$vars['links'] = !empty($node->links) ? theme('links', $node->links, NULL) : '';
$vars['terms'] = theme('links', $vars['taxonomy'], NULL);

// Crunch all attributes into a single string to be added to the block container.
$vars['node_attributes'] = theme('render_attributes', $vars['node_attributes']);

// Handle forum nodes differently
if ($node->type == 'forum' && $vars['page']) {
  // Unset regular node type classes, and add custom forum ones
  unset($vars['node_attributes']);
  // Start over and apply forum specific classes
  $vars['node_attributes'] = array();
  // Shared across forum posts and coments
  $vars['node_attributes']['id'] = 'forum-post-'. $node->nid;
  // Shared across forum posts and coments
  $vars['node_attributes']['class'][] = 'forum-post';
  // Used to distinquish the original post from the coments
  $vars['node_attributes']['class'][] = 'forum-post-top';

  // Add the forums.css file
  $advanced_forum = function_exists('advanced_forum_theme');
  if ($node->type == 'forum' && !$advanced_forum) {
    drupal_add_css(path_to_theme() .'/css/forums.css', 'theme', 'all', TRUE);
    // We are viewing a full page node
    if ($node->page == TRUE) {
      // 0 = Anonymous User
      if ($node->uid != 0) {
        // Load up the real user object
        $vars['account'] = user_load(array('uid' => $node->uid));
        $vars['joined'] = t('<abbr class="created" title="!microdate">@date</abbr>', array(
          '@date' => format_date($vars['account']->created),
          '!microdate' => format_date($vars['account']->created, 'custom', "Y-m-d\TH:i:sO"),
          )
        );
        $vars['name'] = theme('username', $vars['account']);
      }
      else {
        $vars['name'] = theme('username', $node->uid);
      }
    }
  }
  // Crunch all attributes into a single string to be added to the block container.
  $vars['node_attributes'] = theme('render_attributes', $vars['node_attributes']);

}
// Show nodes normally when they appear outside of the forum
elseif ($node->type == 'forum' && !$vars['page']) {
  $vars['template_files'][0] = 'node';
  return;
}