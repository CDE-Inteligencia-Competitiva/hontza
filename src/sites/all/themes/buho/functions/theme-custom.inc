<?php
// $Id$

/**
 * @file
 * Contains custom functions written for the Sky theme
 */

/**
 * Create a string of attributes form a provided array.
 * Credit: Studio http://drupal.org/project/studio
 *
 * @param $attributes
 * @return string
 */
function buho_render_attributes($attributes) {
  if ($attributes) {
    $items = array();
    foreach ($attributes as $attribute => $data) {
      if (is_array($data)) {
        $data = implode(' ', $data);
      }
      $items[] = $attribute .'="'. $data .'"';
    }
    $output = ' '. str_replace('_', '-', implode(' ', $items));
  }
  return $output;
}

/**
 * Conditional Stylesheets
 * Loads alternate stylesheets for Internet Explorer
 */
function buho_conditional_stylesheets() {
  // Targets IE 6 and under
  $output = "\n".'<!--[if lt IE 7.0]><link rel="stylesheet" href="'. base_path() . path_to_theme() .'/css/ie-6.css" type="text/css" media="all" charset="utf-8" /><![endif]-->'."\n";
  // Targets IE 7
  $output .= '<!--[if IE 7.0]><link rel="stylesheet" href="'. base_path() . path_to_theme() .'/css/ie-7.css" type="text/css" media="all" charset="utf-8" /><![endif]-->'."\n";
  return $output;
}

/**
 * CSS Filter
 * Borrowed from Studio ;)
 */
function buho_id_safe($string) {
  // Replace with dashes anything that isn't A-Z, numbers, dashes, or underscores.
  $string = strtolower(preg_replace('/[^a-zA-Z0-9-]+/', '-', $string));
  // If the first character is not a-z, add 'n' in front.
  if (!ctype_lower($string{0})) { // Don't use ctype_alpha since its locale aware.
    $string = 'id'. $string;
  }
  return $string;
}
