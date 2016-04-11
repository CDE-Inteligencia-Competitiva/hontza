<?php
// $Id: taxonomy_additions.views.inc,v 1.1 2011/02/01 00:20:14 amateescu Exp $

/**
 * @file
 * Provides additional views handlers for the Taxonomy module.
 */

/**
 * Implementation of hook_views_plugins().
 */
function taxonomy_additions_views_plugins() {
  return array(
    'module' => 'taxonomy_additions',
    'argument validator' => array(
      'taxonomy_term_related' => array(
        'title' => t('Taxonomy term related'),
        'handler' => 'taxonomy_additions_plugin_argument_validate_taxonomy_term_related',
        'path' => drupal_get_path('module', 'taxonomy_additions') . '/includes',
      ),
    ),
  );
}
