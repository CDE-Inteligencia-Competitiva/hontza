<?php

/**
 * @file
 * Administration settings form area for Facebook Share module
 */

/**
 * Form for settings for the Facebook Share module
 */
function facebookshare_admin_settings() {
  drupal_add_css(drupal_get_path('module', 'facebookshare') . '/facebookshare.admin.css');
  
  $form['facebookshare_types'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Content types'),
    '#description' => t('Which content types to apply the Facebook Share button to.'),
    '#options' => node_get_types('names'),
    '#default_value' => variable_get('facebookshare_types', array()),
  );
  $form['facebookshare_location'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Location'),
    '#description' => t('Where to show the Facebook Share button.'),
    '#options' => array(
      'content' => t('Full view'),
      'teasers' => t('Teasers'),
    ),
    '#default_value' => variable_get('facebookshare_location', array()),
  );
  $form['facebookshare_weight'] = array(
    '#type' => 'textfield',
    '#title' => t('Weight'),
    '#description' => t('The weight of which the Facebook widget should appear on the content.'),
    '#default_value' => variable_get('facebookshare_weight', '-10'),
    '#size' => 5,
  );
  $sizes = array('box_count' => '', 'button_count' => '', 'button' => '', 'icon_link' => '', 'icon' => '');
  foreach ($sizes as $size => $button) {
    $sizes[$size] = theme(
      'facebookshare_button',
      'http://www.facebook.com/facebook-widgets/share.php',
      $size,
      variable_get('facebookshare_text', '')
    );
  }
  $form['facebookshare_size'] = array(
    '#type' => 'radios',
    '#title' => t('Button size'),
    '#required' => TRUE,
    '#options' => $sizes,
    '#default_value' => variable_get('facebookshare_size', ''),
  );
  $form['facebookshare_text'] = array(
    '#type' => 'textfield',
    '#title' => t('Share button text'),
    '#default_value' => variable_get('facebookshare_text', ''),
    '#description' => t('Leave blank for default "Share" text'),
  );
  
  return system_settings_form($form);
}