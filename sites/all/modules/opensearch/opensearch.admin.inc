<?php
// $Id: opensearch.admin.inc,v 1.1.2.28 2010/09/26 23:30:14 kiam Exp $


/**
 * @file
 * Admin page callbacks for the OpenSearch feed module.
 */

/**
 * Form builder for the settings form.
 *
 * @see opensearch_admin_settings_reset()
 * @see opensearch_admin_settings_submit()
 * @see theme_opensearch_description_files()
 * @ingroup forms
 */
function opensearch_admin_settings() {
  $enabled_feeds = variable_get('opensearch_enabled_feeds', NULL);
  $enabled_types = variable_get('opensearch_enabled_types', NULL);
  $form = array(
    '#opensearch_type' => '-default-',
    '#submit' => array('opensearch_admin_settings_submit'),
  );

  $options = array();
  $settings = opensearch_load_settings();
  $standard_value = array();

  if (!variable_get('opensearch_dismiss_tokens_warning', FALSE)) {
    if (!module_exists('token')) {
      drupal_set_message(
        t('The Token module is not enabled; if you use tokens for these settings, they will not be replaced. <a href="@dismiss">Dismiss this warning.</a>', array('@dismiss' => url('opensearch/dismiss_warning'))),
        'warning'
      );
    }
    elseif (!module_exists('opensearch_token')) {
      drupal_set_message(
        t('The Opensearch tokens module is not enabled; by default, the Opensearch feed module settings use tokens defined by that module. <a href="@dismiss">Dismiss this warning.</a>', array('@dismiss' => url('opensearch/dismiss_warning'))),
        'warning'
      );
    }
  }

  $form['description_files'] = array(
    '#type' => 'fieldset',
    '#title' => t('Description files'),
    '#theme' => 'opensearch_description_files'
  );

  foreach (module_implements('search') as $module) {
    // Do not consider modules that don't return a name.
    $name = module_invoke($module, 'search', 'name');
    if (empty($name)) {
      continue;
    }

    $bool = (
      isset($enabled_types) &&
      in_array($module, array_filter($enabled_types)) ||
      !isset($enabled_types)
    );
    $standard_value[] = $module;

    if ($bool) {
      $form['description_files']['titles'][$module] = array(
        '#value' => l($name, "opensearch/$module")
      );

      $form['description_files']['operations'][$module] = array(
        '#value' => l(t('edit settings'), "admin/settings/search/opensearch/$module")
      );
    }
    else {
      $form['description_files']['titles'][$module] = array(
        '#value' => $name
      );

      $form['description_files']['operations'][$module] = array(
        '#value' => ' '
      );
    }

    $options[$module] = '';
  }

  $form['description_files']['opensearch_enabled_types'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Enabled'),
    '#options' => $options,
    '#default_value' => isset($enabled_types) ? $enabled_types : $standard_value,
    '#checkall' => 'opensearch-enabled-types',
  );

  $form['description_files']['opensearch_enabled_feeds'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Enabled feeds'),
    '#options' => $options,
    '#default_value' => isset($enabled_feeds) ? $enabled_feeds : $standard_value,
    '#checkall' => 'opensearch-enabled-feeds',
  );

  $form['default_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Default settings'),
    '#collapsible' => TRUE,
  );

  $form['default_settings'] += _opensearch_admin_settings_fields();

  $form['buttons']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save configuration'),
  );

  $form['buttons']['reset'] = array(
    '#type' => 'submit',
    '#value' => t('Reset to defaults'),
    '#submit' => array('opensearch_admin_settings_reset'),
  );

  return $form;
}

/**
 * Submission handler for opensearch_admin_settings().
 *
 * @see opensearch_admin_settings()
 */
function opensearch_admin_settings_reset($form, &$form_state) {
  db_query("DELETE FROM {opensearch} WHERE type = '%s'", $form['#opensearch_type']);

  if ($form['#opensearch_type'] == '-default-') {
    variable_del('opensearch_enabled_feeds');
    variable_del('opensearch_enabled_types');
  }

  if (!variable_get('menu_need_rebuild', FALSE)) {
    variable_set('menu_need_rebuild', TRUE);
  }

  drupal_set_message(t('The settings have been reset.'), 'status');
}

/**
 * Submission handler for opensearch_admin_settings().
 *
 * @see opensearch_admin_settings()
 */
function opensearch_admin_settings_submit($form, &$form_state) {
  $type = $form['#opensearch_type'];

  if ($type == '-default-') {
    variable_set(
      'opensearch_enabled_feeds',
      $form_state['values']['opensearch_enabled_feeds']
    );
    variable_set(
      'opensearch_enabled_types',
      $form_state['values']['opensearch_enabled_types']
    );

    unset($form_state['values']['opensearch_enabled_feeds']);
    unset($form_state['values']['opensearch_enabled_types']);
  }
  else {
    $form_state['redirect'] = 'admin/settings/search/opensearch';
  }

  opensearch_save_settings(
    $form_state['values'], $type
  );

  drupal_set_message(t('The settings have been changed.'), 'status');
}

/**
 * Form builder for the search type settings form.
 *
 * @see opensearch_admin_settings_reset()
 * @see _opensearch_admin_settings_fields()
 * @ingroup forms
 */
function opensearch_admin_type_settings($form_state, $info) {
  $form = array(
    '#opensearch_type' => $info->type,
    '#submit' => array('opensearch_admin_settings_submit'),
  );

  $form += _opensearch_admin_settings_fields($info->type);

  $form['buttons']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save configuration'),
  );

  $form['buttons']['reset'] = array(
    '#type' => 'submit',
    '#value' => t('Reset to defaults'),
    '#submit' => array('opensearch_admin_settings_reset'),
  );

  return $form;
}

/**
 * Returns the form fields common to the settings forms.
 *
 * @param $type
 *   The settings type for which return the form fields.
 *
 * @return
 *   The array of form API fields.
 */
function _opensearch_admin_settings_fields($type = '-default-') {
  $form = array();
  $settings = opensearch_load_settings($type);

  $form['shortname'] = array(
    '#type' => 'textarea',
    '#title' => t('Short name'),
    '#default_value' => $settings['shortname'],
    '#description' =>
      t("A brief name that will appear in buttons, UI controls, etc., that reference this search content provider.") . ' ' .
      t("Any HTML tags will be removed, and the string truncated to !length characters.", array('!length' => 16)),
    '#cols' => 30,
    '#required' => TRUE,
    '#weight' => -1,
  );

  $form['longname'] = array(
    '#type' => 'textarea',
    '#title' => t('Long name'),
    '#default_value' => $settings['longname'],
    '#description' =>
      t("The name used to refer to this search content provider in hypertext links, etc.") . ' ' .
      t("Any HTML tags will be removed, and the string truncated to !length characters.", array('!length' => 48)),
    '#cols' => 30,
  );

  $form['description'] = array(
    '#type' => 'textarea',
    '#title' => t('Description'),
    '#default_value' => $settings['description'],
    '#description' =>
      t("A human readable text description of the search content provider.") . ' ' .
      t("Any HTML tags will be removed, and the string truncated to !length characters.", array('!length' => 1024)
    ),
    '#cols' => 30,
    '#required' => TRUE,
  );

  $form['contact'] = array(
    '#type' => 'textarea',
    '#title' => t('Contact'),
    '#default_value' => $settings['contact'],
    '#description' =>
      t("An email address at which the developer can be reached.") . ' ' .
      t("Any HTML tags will be removed."),
    '#cols' => 30,
  );

  $form['tags'] = array(
    '#type' => 'textarea',
    '#title' => t('Tags'),
    '#default_value' => $settings['tags'],
    '#description' =>
      t("A space-delimited set of words that are used as keywords to identify and categorize this site search content.") . ' ' .
      t("Any HTML tags will be removed, and the string truncated to !length characters.", array('!length' => 256)),
    '#cols' => 30,
  );

  $form['attribution'] = array(
    '#type' => 'textarea',
    '#title' => t('Attribution'),
    '#default_value' => $settings['attribution'],
    '#description' =>
      t("A list of all content sources or platforms that should be credited.") . ' ' .
      t("Any HTML tags will be removed, and the string truncated to !length characters.", array('!length' => 256)),
    '#cols' => 30,
  );

  $form['image_type'] = array(
    '#type' => 'radios',
    '#title' => t('Image'),
    '#default_value' => $settings['image_type'],
    '#options' => array(
      'theme' => t('Use the logo icon used by the enabled theme'),
      'custom' => t('Use a custom image'),
    ),
  );

  $form['image_custom'] = array(
    '#type' => 'textarea',
    '#default_value' => $settings['image_custom'],
    '#description' =>
      t("The path of a image file that will be used in the description file.") . ' ' .
      t("Any HTML tags will be removed."),
    '#cols' => 30,
  );

  $form['syndication_right'] = array(
    '#type' => 'radios',
    '#title' => t('Syndication right'),
    '#default_value' => $settings['syndication_right'],
    '#options' => array(
      'open' => t('Open search — results can be published or re-published without restriction'),
      'limited' => t('Limited search — results can be published on the client site'),
      'private' => t('Private search — feed may be queried, but the results may not be displayed on the client site'),
      'closed' => t('Closed search —  feed should not be queried, and will disable the column for searches'),
    ),
    '#description' => t('The degree to which the search results provided by this search engine can be distributed.'),
  );

  $form['adult_content'] = array(
    '#type' => 'radios',
    '#title' => t('Adult content'),
    '#options' => array(
      0 => t('The search results don not contain adult content'),
      1 => t('The search results contain adult content'),
    ),
    '#default_value' => $settings['adult_content'],
  );

  $form['rss_title'] = array(
    '#type' => 'textarea',
    '#title' => t('RSS title'),
    '#default_value' => $settings['rss_title'],
    '#description' =>
      t("The title used in the RSS feed.") . ' ' .
      t("Any HTML tags will be removed."),
    '#cols' => 30,
  );

  $form['rss_description'] = array(
    '#type' => 'textarea',
    '#title' => t('RSS description'),
    '#default_value' => $settings['rss_description'],
    '#description' =>
      t("The description used in the RSS feed.") . ' ' .
      t("Any HTML tags will be removed."),
    '#cols' => 30,
  );

  $form['token_help'] = array(
    '#type' => 'fieldset',
    '#title' => t('Replacement patterns'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#description' => t('Prefer raw-text replacements for text to avoid problems with HTML entities.'),
  );

  $tokens = array('global');
  if (module_exists('opensearch_token')) {
    $tokens[] = 'search';
  }

  $form['token_help']['help'] = array(
    '#value' => theme('token_help', $tokens),
  );

  return $form;
}

/**
 * Avoids the warning about tokens.module not being enabled is presented in the
 * settings page.
 */
function opensearch_dismiss_warning() {
  variable_set('opensearch_dismiss_tokens_warning', FALSE);
  drupal_goto('admin/settings/search/opensearch');
}

/**
 * Renders the list of the enabled Opensearch feeds.
 *
 * @param $form
 *   The form containing the fields to render.
 *
 * ingroup themeable
 */
function theme_opensearch_description_files($form) {
  $header = array();
  $rows = array();

  if (is_array($form['titles'])) {
    foreach (element_children($form['titles']) as $key) {
      $row = array();
      $row[] = drupal_render($form['titles'][$key]);
      $row[] = drupal_render($form['opensearch_enabled_types'][$key]);
      $row[] = drupal_render($form['opensearch_enabled_feeds'][$key]);
      $row[] = drupal_render($form['operations'][$key]);

      $rows[] = $row;
    }

    if (empty($rows)) {
      $rows = array(
        array(
          'data' => t('There are no modules implementing search hooks.'),
          'colspan' => '4',
        )
      );
    }
  }

  $header[] = t('Type');
  $header[] = array(
    'data' => $form['opensearch_enabled_types']['#title'],
    'class' => count($rows) > 1 ? 'opensearch-enabled-types' : '',
  );
  $header[] = array(
    'data' => $form['opensearch_enabled_feeds']['#title'],
    'class' => count($rows) > 1 ? 'opensearch-enabled-feeds' : '',
  );
  $header[] = t('Operations');

  unset($form['opensearch_enabled_feeds']['#title']);
  unset($form['opensearch_enabled_types']['#title']);

  return theme('table', $header, $rows) . drupal_render($form);
}
