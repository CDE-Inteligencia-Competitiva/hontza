<?php
/**
 * help.php - Displays help page.
 *
 */
require_once './includes/bootstrap.inc';
// Get URL parameters.
$nid   = $_GET['nid'];

// Load drupal and initialize theme so that the popup takes the site theme
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
theme();

// We put the css and content into a page.tpl template
$css = drupal_add_css();
$query_string = '?' . substr(variable_get('css_js_query_string', '0'), 0, 1);
foreach ($css as $media => $types) {
  foreach ($types as $type => $files) {
    foreach ($types[$type] as $file => $preprocess) {
      // Only include the stylesheet if it is a theme stylesheet.
      if ($type == "theme" && file_exists($file)) {
        $styles .= '<link type="text/css" rel="stylesheet" media="' . $media . '" href="' . base_path() . $file . $query_string . '" />' . "\n";
      }
    }
  }
}

if ($nid > 0) {
  $node = node_load($nid);
  $helpfound = TRUE;
}

// Display an error if necessary.
if (!$helpfound) {
  $node->title = t('Error');
  $node->body = t('Help file could not be found!');
}
//  These might be useful in the template
$node->height = $_GET['h'];
$node->width = $_GET['w'];

if ($_GET['type'] == "standard") print theme('help_popup', $styles, $node);
else print theme('help_popup_js', $node);