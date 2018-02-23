<?php
// $Id$

/**
 * @file
 * Passes vars to page.tpl.php.
 *
 * @see template_preprocess_page(), templates/overrides/page.tpl.php
 * http://api.drupal.org/api/function/template_preprocess_page/6
 *
 * @return $vars
 */

global $user;

/**
 * Create a variable that outputs the primary links as a menu tree
 */
$vars['ie_styles'] = theme('conditional_stylesheets');

/**
 * Drop-down menu
 * Create a variable that outputs the primary links as a menu tree
 */
if ($vars['primary_links']) {
  // Find out which menu is being used for primary links and create the menu tree
  $pid = variable_get('menu_primary_links_source', 'primary-links');
  $tree = menu_tree($pid);
  $tree = str_replace(' class="menu"', '', $tree);

  // Find out how the navigation is aligned, and prepard the output
  $navigation_alignment = theme_get_setting('buho_nav_alignment');
  $vars['primary_links'] = '<del class="wrap-'. $navigation_alignment .'">'. $tree .'</del>';
}
//echo print_r($vars['primary_links'],1);
/**
 * Add some special classes for handling $site_name, $logo and $site_slogan
 */
$vars['header_attributes'] = array();
$vars['header_attributes']['id'] = 'header';
$vars['header_attributes']['class'][] = 'layout-region';
switch (TRUE) {
  // Logo, Name & Slogan
  case($vars['logo'] && $vars['site_name'] && $vars['site_slogan']):
    $vars['header_attributes']['class'][] = 'name-slogan-logo';
    break;
  // Logo & Name
  case($vars['logo'] && $vars['site_name'] && empty($vars['site_slogan'])):
    $vars['header_attributes']['class'][] = 'name-logo';
    break;
  // Name & Slogan
  case($vars['site_name'] && empty($vars['logo']) && $vars['site_slogan']):
    $vars['header_attributes']['class'][] = 'name-slogan';
    break;
  // Slogan & Logo
  case(empty($vars['site_name']) && $vars['logo'] && $vars['site_slogan']):
    $vars['header_attributes']['class'][] = 'logo-slogan';
    break;
}

/**
 * Create an array to will contain body classes and id's
 */
$body_attributes = array();

/**
 * Grab data from arguments to build a catch_all set of classes.
 * Special cases are addressed below in the arg(0) switch, but this aims
 * to be a catch-all class to account for everything else
 */
$index = 0;
$arguments = array();

while (($argument_value = arg($index)) != '') {
  // Exclude values that are numeric (i.e node id's, user id's)
  if (!is_numeric($argument_value)) {
    $arguments[$index] = check_plain($argument_value);
  }
  $index++;
}

$catch_all = implode('-', preg_replace('/[\s_]+/', '-', $arguments));

/**
 * Handle special cases for CSS classes and id attributes.
 */

switch (arg(0)) {

  case 'admin':
    // Add a common class across admin pages
    $vars['body_attributes']['class'][] = 'section-admin';
    // Use the catch-all variable as an ID
    $vars['body_attributes']['id'][] = 'page-'. $catch_all;
  break;

  case 'blog':
    // Add a common class across user blogs
    $vars['body_attributes']['class'][] = 'section-blog';
  break;

  case 'comment':
      $vars['body_attributes']['class'][] = 'page-comment';
  break;

  case 'contact':
    // Add a common class across contact pages
    $vars['body_attributes']['id'][] = 'page-contact';
    $vars['body_attributes']['class'][] = 'page-form';

    // Support for http://drupal.org/project/contact_forms
    if (function_exists('contact_forms_menu')) {
      // Set a common class across each contact category
      $vars['body_attributes']['class'][] = 'page-contact';
      if (arg(1)) {
        // Use the catch-all to grab a class for each category
        $vars['body_attributes']['class'][] = 'page-'. $catch_all;
      }
    }
  break;

  case 'node':
    if ($node = menu_get_object()) {
      // Add a the node type and common .page-node class
      $vars['body_attributes']['class'][] = 'page-node';
      // Add a page-level node type class
      $vars['body_attributes']['class'][] = 'page-'. $node->type;
      if ($node->type == 'blog') {
        // Add a common class across user blogs
        $vars['body_attributes']['class'][] = 'section-blog';
      }
      if (!arg(2)) {
        // We are viewing a node, so add an ID
        $vars['body_attributes']['id'][] = 'page-node-'. $node->nid;
        // Also add a class to distinguish viewing a node vs. editing, etc.
        $vars['body_attributes']['class'][] = 'page-node-view';
      }
      else {
        // Catch other node actions, i.e. edit, preview, delete, load, clone, etc.
        $vars['body_attributes']['class'][] = 'page-'. $catch_all;
        // Catch "edit" specifically for a .page-form class
        if (arg(2) == 'edit' || arg(2) == 'outline') {
          $vars['body_attributes']['class'][] = 'page-form';
        }
      }
    }
    elseif (arg(1) == 'add') {
      // We aren't viewing a node, but instead the node/add page
      $vars['body_attributes']['class'][] = 'page-form';
    }

  break;

  case 'user':
    // Add a common class across user pages
    $vars['body_attributes']['class'][] = 'section-user';

    if (arg(0) == 'user' && $user->uid == 0 && !is_numeric(arg(1))) {
      switch (arg(1)) {
        // Login
        case NULL:
        case 'login':
          $vars['body_attributes']['id'][] = 'page-user-login';
        break;
        // Registration
        case 'register':
          $vars['body_attributes']['id'][] = 'page-user-register';
        break;
        // Password Recovery
        case 'password':
          $vars['body_attributes']['id'][] = 'page-user-password';
        break;
      }
    }
    else {
      // Account for other user pages like the "contact" and "open id" tabs
      $vars['body_attributes']['class'][] = 'page-'. $catch_all;
      }
  break;

  default:
    $vars['body_attributes']['id'][] = 'page-'. $catch_all;
  break;
}

// Provide a class stating whether the user is logged in or not
$vars['body_attributes']['class'][] = $vars['logged_in'] ? 'registered-user' : 'anonymous-user';

// Special handling for the front page
if ($vars['is_front']) {
  // Unset the ID so it doesn't inherit anything from above
  unset($vars['body_attributes']['id']);
  // Add a simple "homepage" ID
  $vars['body_attributes']['id'][] = 'homepage';
}

/**
 * Rebuild the layout classes.
 * This ensures any regions that might be disabled during
 * preprocess_page() are properly accounted for.
 */
switch (TRUE) {
  case $vars['left'] && empty($vars['right']):
    // Sidebar left contains content and sidebar right does not
    $vars['body_attributes']['class'][] = 'left';
  break;
  case empty($vars['left']) && $vars['right']:
    // Sidebar right contains content and sidebar left does not
    $vars['body_attributes']['class'][] = 'right';
  break;
  case empty($vars['left']) && empty($vars['right']):
    // Both regions are empty
    $vars['body_attributes']['class'][] = 'no-sidebars';
  break;
  case $vars['left'] && $vars['right']:
    // Both regions contain content
    $vars['body_attributes']['class'][] = 'both';
  break;
}
//intelsat-2015
if(red_is_new_font()){    
    $vars['body_attributes']['class'][]=' new-font-body';
}else if(hontza_canal_rss_is_visualizador_activado()){
    $vars['body_attributes']['class'][]=' visualizador-body';
}else{
    $vars['body_attributes']['class'][]=' class-body';
}
// Crunch down our attributes into a single string to be appended to the body
// tag in the page template (credit Studio theme ;)).
$vars['body_attributes'] = theme('render_attributes', $vars['body_attributes']);
$vars['header_attributes'] = theme('render_attributes', $vars['header_attributes']);
//intelsat-2015
if(hontza_canal_rss_is_visualizador_activado()){
    publico_add_page_info($vars);
}				