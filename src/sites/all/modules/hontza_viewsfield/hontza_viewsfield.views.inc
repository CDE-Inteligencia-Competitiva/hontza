<?php 
//$Id: hontza_viewsfield,v 1.1.4.2 2010/31/09 18:32:33 investic Exp $

/**
 * Implementation of hook_views_handlers().
 *
 * @return array
 */

function hontza_viewsfield_views_handlers() {
  return array(
    'info' => array(
      'path' => drupal_get_path('module', 'hontza_viewsfield') .'/includes',
    ),
    'handlers' => array(
      'hontza_viewsfield_handler_field_canales' => array(
        'parent' => 'views_handler_field',
      ),
    ),
  );
}

/**
 * Implementation of hook_views_data().
 *
 * @return array
 */
function hontza_viewsfield_views_data() {
  $data['viewsfield']['table']['group'] = t('Viewsfield');
  $data['viewsfield']['table']['join'] = array(
    '#global' => array(),
  );

  $data['viewsfield']['canales'] = array(
    'title' => t('Own'),
    'help' => t('Customised field.'),
    'field' => array(
      'handler' => 'hontza_viewsfield_handler_field_canales',
      'click sortable' => FALSE,
      'notafield' => TRUE,
    ),
  );
  
  return $data;
}
