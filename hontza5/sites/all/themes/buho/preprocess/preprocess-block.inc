<?php
// $Id$

/**
 * @file
 * Passes variables to block.tpl.php.
 *
 * @see template_preprocess_block(), templates/overrides/block.tpl.php
 * http://api.drupal.org/api/function/template_preprocess_block/6
 *
 * @return $vars
 */
/**
 * Use $block as opposed to $vars['block].
 * Helps me to separate what's there vs. what I've created.
 */
$block = $vars['block'];

// Prepare the variables that will store the attributes while being processed
$vars['block_attributes'] = $block->attributes ? $block->attributes : array();

// Add an id for specific block styling.
$vars['block_attributes']['id'] = 'block-'. $block->module .'-'. $block->delta;

// Add a common class to all blocks.
$vars['block_attributes']['class'][] = 'block';
// Add a class to allow for styling of blocks produced by a specific module.
$vars['block_attributes']['class'][] = 'block-'. $block->module;
// Add a class to allow for specific styling of odd and even blocks.
$vars['block_attributes']['class'][] = 'block-'. $vars['zebra'];
// Add a class to allow for specific styling of odd and even regions.
$vars['block_attributes']['class'][] = 'region-'. $vars['block_zebra'];
// Make block containers self-clearing.
$vars['block_attributes']['class'][] = 'clearfix';

// Identify some core module blocks that contain menus
switch ($block->module) {
  case 'menu':
  case 'user':
    // Menu and Navigation blocks
    if ($block->delta == 1) {
      $vars['block_attributes']['class'][] = 'style-menu';
    }
  case 'book':
    // Book Navigation
    if ($block->delta == 0) {
      $vars['block_attributes']['class'][] = 'style-menu';
    }
  break;
}

// Add support for the Skinr module.
$vars['block_attributes']['class'][] = $vars['skinr'];

// Crunch all attributes into a single string to be added to the block container.
$vars['block_attributes'] = theme('render_attributes', $vars['block_attributes']);
