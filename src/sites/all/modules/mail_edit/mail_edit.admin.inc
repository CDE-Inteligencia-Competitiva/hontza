<?php

/**
 * @file
 * Administrative interface for the Mail Editor module.
 */

/**
 * Form builder for the template list.
 *
 * @return array
 */
function mail_edit_list_form() {
  $form['mail_templates'] = mail_edit_list();
  return $form;
}

/**
 * Page callback: List mail templates.
 *
 * @return array
 */
function mail_edit_list() {
  _mail_edit_include();
  if (module_exists('locale')) {
    $language_list = locale_language_list('name', TRUE);
  }
  else {
    foreach (array_keys(language_list()) as $key) {
      $language_list[$key] = $key;
    }
  }
  $languages_selected = !empty($_SESSION['mail_edit']['languages_selected']) ? $_SESSION['mail_edit']['languages_selected'] : $language_list;
  $module = isset($_SESSION['mail_edit']['module']) ? $_SESSION['mail_edit']['module'] : '-enabled';

  $form['filter'] = mail_edit_list_filter($languages_selected, $module, $language_list);
  $form['filtered_form'] = mail_edit_list_filtered_form($languages_selected, $module, count($language_list) > 1);
  drupal_add_css(drupal_get_path('module', 'mail_edit') . '/mail_edit.admin.css');
  $form['#submit'][] = 'mail_edit_list_submit';
  return $form;
}

/**
 * Filter the mail edit table.
 *
 * @param array $languages_selected
 *   The list of selected langauges.
 * @param $module
 *   The selected module option.
 * @return array
 *   The $form array.
 */
function mail_edit_list_filter($languages_selected, $module, $language_list) {
  if (count($language_list) > 1) {
    $form['languages_selected'] = array(
      '#type'          => 'select',
      '#title'         => t('Languages'),
      '#description'   => t('Select the languages to display.'),
      '#options'       => $language_list,
      '#default_value' => array_keys($languages_selected),
      '#multiple'      => TRUE,
    );
  }

  $module_list = _mail_edit_module_list(TRUE);
  $options['-enabled'] = t('- Display enabled modules only -');
  $options['-all'] = t('- Display all modules -');
  $result = db_query('SELECT DISTINCT(module) FROM {mail_edit_registry} ORDER BY module');
  while ($row = db_fetch_object($result)) {
    $options[$row->module] = isset($module_list[$row->module]) ? $module_list[$row->module] : check_plain($row->module);
  }

  $form['module'] = array(
    '#type'          => 'select',
    '#title'         => t('Module'),
    '#description'   => t('Select a module.'),
    '#options'       => $options,
    '#default_value' => $module,
  );

  $form['submit'] = array(
    '#type'  => 'item',
    '#value' => '',
  );
  $form['submit']['filter'] = array(
    '#type'  => 'submit',
    '#value' => t('Filter'),
  );
  $form['language_list'] = array(
    '#type'  => 'hidden',
    '#value' => serialize($language_list),
  );

  $form['#theme'] = 'mail_edit_list_filter';
  return $form;
}

/**
 * Create $form array with filtered list.
 *
 * @param $languages
 *   The languages to include.
 * @param $module
 *   The module option to restrict the list with.
 * @return array
 *   The $form array.
 */
function mail_edit_list_filtered_form($languages_selected, $module, $display_language_names) {
  _mail_edit_key_registry_rebuild();

  $header = _mail_edit_list_get_header($languages_selected, $display_language_names);

  switch ($module) {
    case '-all':
      $sql = "SELECT mer.id, mer.module, mer.mailkey, mer.description FROM {mail_edit_registry} mer ORDER BY mer.module, mer.mailkey";
      $result = pager_query($sql, 50, 0);
      break;

    case '-enabled':
      $enabled_modules = array_keys(_mail_edit_module_list());
      $placeholders = db_placeholders($enabled_modules, 'varchar');
      $sql = "SELECT mer.id, mer.module, mer.mailkey, mer.description FROM {mail_edit_registry} mer WHERE mer.module IN (" . $placeholders . ") ORDER BY mer.module, mer.mailkey";
      $result = pager_query($sql, 50, 0, NULL, $enabled_modules);
      break;

    default:
      $sql = "SELECT mer.id, mer.module, mer.mailkey, mer.description FROM {mail_edit_registry} mer WHERE mer.module = '%s' ORDER BY mer.module, mer.mailkey";
      $result = pager_query($sql, 50, 0, NULL, $module);
    break;
  }

  $form['templates']['#tree'] = TRUE;
  $form['#cache'] = TRUE;

  $form['header'] = array(
    '#type'  => 'value',
    '#value' => $header,
  );

  $form['templates'] = array();
  while ($template = db_fetch_object($result)) {
    // Add all translated languages to template.
    $result2 = db_query("SELECT language FROM {mail_edit} WHERE id = '%s'", $template->id);
    while ($template2 = db_fetch_object($result2)) {
      $template->languages[$template2->language] = TRUE;
    }
    $form['templates'][$template->id] = _mail_edit_list_row($template, $languages_selected, $display_language_names);
  }

  $form['languages'] = array(
    '#type'  => 'value',
    '#value' => $languages_selected,
  );


  $form['pager'] = array('#value' => theme('pager', NULL, 50, 0));
  $form['#theme'] = 'mail_edit_table';
  return $form;
}

/**
 * Build the header for the mail edit table.
 *
 * @param array $languages_selected
 *   The languages to include.
 * @param bool $display_language_names
 *   Whether to display the name of the langauge.
 * @return array
 */
function _mail_edit_list_get_header($languages_selected, $display_language_names) {
  $header = array(
    array('data' => t('Module')),
    array('data' => t('Mailkey')),
  );

  foreach ($languages_selected as $lang_name) {
    $header[] = array('data' => ($display_language_names ? $lang_name : t('Operations')));
  }
  return $header;
}

/**
 * Build one row of the mail edit table.
 *
 * @param $template
 *   The mail template for this row.
 * @param $languages
 *   The languages to include.
 * @return array
 */
function _mail_edit_list_row($template, $languages, $display_language_names) {
  $module_list = _mail_edit_module_list(TRUE);
  $form['module'] = array(
    '#type'  => 'markup',
    '#value' => isset($module_list[$template->module]) ? $module_list[$template->module] : check_plain($template->module),
  );

  $form['id'] = array(
    '#type'  => 'value',
    '#value' => check_plain($template->id),
  );
  $form['mailkey'] = array(
    '#type'  => 'markup',
    '#value' => check_plain($template->mailkey),
  );
  $form['description'] = array(
    '#type'  => 'markup',
    '#value' => filter_xss_admin($template->description),
  );
  $form['operations'] = array(
    '#type'  => 'markup',
    '#value' => '',
  );
  foreach ($languages as $lang_code => $lang_name) {
    $variable = array('@language' => $lang_code);
    if ($display_language_names) {
      $link_text = t(isset($template->languages[$lang_code]) ? 'Edit @language' : 'Add @language', $variable);
    }
    else {
      $link_text = t(isset($template->languages[$lang_code]) ? 'Edit' : 'Add');
    }
    $form[$lang_code] = array(
      '#type'  => 'markup',
      '#value' => l($link_text, 'admin/build/mail-edit/'. $template->id .'/'. $lang_code),
    );
  }
  return $form;
}

/**
 * The submit handler for mail_edit_list_form().
 *
 * @param $form
 * @param $form_state
 */
function mail_edit_list_form_submit($form, &$form_state) {
  $language_list = unserialize($form_state['values']['language_list']);
  $_SESSION['mail_edit']['module'] = $form_state['values']['module'];
  if (isset($form_state['values']['languages_selected'])) {
    $_SESSION['mail_edit']['languages_selected'] = array_intersect_key($language_list, $form_state['values']['languages_selected']);
  }
}

/**
 * The theme function for the basic filter form.
 *
 * @param $form
 * @return string
 *
 * @ingroup themeable
 */
function theme_mail_edit_list_filter($form) {
  $output = '<div id="mail-edit-filter">';
  foreach (element_children($form) as $key) {
    $attributes = drupal_attributes(array(
      'id'    => 'mail-edit-'. str_replace('_', '-', $key) .'-filter',
      'class' => 'mail-edit-filter',
    ));
    $output .= "<div $attributes>";
    $output .= drupal_render($form[$key]);
    $output .= '</div>';
  }
  $output .= '</div>';

  return $output;
}

/**
 * The theme function for the mail edit table.
 *
 * @param $form
 * @return string
 *
 * @ingroup themeable
 */
function theme_mail_edit_table($form) {
  $rows = array();
  $header = $form['header']['#value'];
  $languages = $form['languages']['#value'];

  foreach (element_children($form['templates']) as $key) {
    // Build the table row.
    $row = array();
    $row['data'][] = array('data' => drupal_render($form['templates'][$key]['module']));
    $row['data'][] = array('data' => drupal_render($form['templates'][$key]['mailkey']) . '<div class="description">' . drupal_render($form['templates'][$key]['description']) . '</div>');
    foreach ($languages as $lang_code => $lang_name) {
      $row['data'][] = array('data' => drupal_render($form['templates'][$key][$lang_code]), 'class' => 'mail-edit-table-'. $lang_code);
    };
    $row['data'][] = array('data' => drupal_render($form['templates'][$key]['operations']));
    $rows[] = $row;
  }

  if (empty($rows)) {
    $rows[] = array(array(
      'data'    => t('No templates available for override.'),
      'colspan' => count($header),
    ));
  }
  $output = theme('table', $header, $rows, array('id' => 'mail-edit-table'));
  if ($form['pager']['#value']) {
    $output .= drupal_render($form['pager']);
  }
  return $output;
}

/**
 * Builds the list of modules that expose mailkeys.
 *
 * @param bool $all
 *   Return all modules if TRUE, or only enabled ones otherwise.
 * @return array
 */
function _mail_edit_module_list($all = FALSE) {
  static $modules;

  if (isset($modules[$all])) {
    return $modules[$all];
  }

  $modules[$all] = array();
  if ($all) {
    $sql = "SELECT name, info FROM {system} WHERE type = 'module' ORDER BY name ASC";
  }
  else {
    $sql = "SELECT name, info FROM {system} WHERE type = 'module' AND status = 1 ORDER BY name ASC";
  }
  $result = db_query($sql);
  while ($module = db_fetch_object($result)) {
    $info = unserialize($module->info);
    $modules[$all][$module->name] = $info['name'];
  }
  return $modules[$all];
}

function mail_edit_trans(&$form_status, $id, $lang) {
  _mail_edit_include();
  $query = "SELECT * FROM {mail_edit_registry} WHERE id = '%s'";
  $result = db_query($query, $id);
  $mailkey_obj = db_fetch_object($result);
  $mailkey = $mailkey_obj->id;

  $query = "SELECT * FROM {mail_edit} WHERE id = '%s' AND language = '%s'";
  $result = db_query($query, $mailkey, $lang);
  // If template is set, then we need to update, if its not set then we need to insert.
  $template = db_fetch_object($result);
  if ($template) {
    $update = TRUE;
  }
  else {
    $update = FALSE;
  }
  $language = new stdClass();
  $language->language = $lang;
  $mail_data = module_invoke($mailkey_obj->module, 'mail_edit_text', $mailkey_obj->mailkey, $language);
  if (!isset($template->subject)) {
    $template->subject = $mail_data['subject'];
  }
  if (!isset($template->body)) {
    $template->body = $mail_data['body'];
  }

  $variables = array('%mailkey' => $mailkey, '%language' => $lang);
  if ($update) {
    $message = t('Update %mailkey template for %language', $variables);
  }
  else {
    $message = t('Create new %mailkey template for %language', $variables);
  }
  drupal_set_title($message);

  $tokens = array();
  $tokens = module_invoke($mailkey_obj->module, 'mail_edit_tokens_list', $mailkey_obj->mailkey);
  // Allow other modules to alter tokens for specific mailkey id.
  drupal_alter($mailkey_obj->id, $tokens);

  $form = array();
  $form['update'] = array(
    '#type'  => 'hidden',
    '#value' => $update,
  );
  $form['language'] = array(
    '#type'  => 'hidden',
    '#value' => $lang,
  );
  $form['id'] = array(
    '#type'  => 'hidden',
    '#value' => $id,
  );
  $form['description'] = array(
    '#title'         => t('Description'),
    '#type'          => 'textfield',
    '#default_value' => isset($template->description) ? $template->description : (isset($mailkey_obj->description) ? filter_xss($mailkey_obj->description, array()) : ''),
  );
  $form['mail'] = array(
    '#type'          => 'fieldset',
    '#title'         => t('Template'),
    '#collapsible'   => TRUE,
  );
  $form['mail']['subject'] = array(
    '#title'         => t('Subject'),
    '#type'          => 'textfield',
    '#default_value' => isset($template->subject) ? $template->subject : '',
  );
  $form['mail']['body'] = array(
    '#title'         => t('Body'),
    '#type'          => 'textarea',
    '#default_value' => isset($template->body) ? $template->body : '',
    '#rows' => 15,
  );
  $placeholders = $tokens;
  // Display the user documentation of placeholders supported by this module mailkey.
  $doc = "<dl>\n";
  foreach ($placeholders as $name => $description) {
    $doc .= '<dt>'. $name .'</dt>';
    $doc .= '<dd>'. $description .'</dd>';
  }
  $doc .= "</dl>\n";
  $form['mail']['token_help'] = array(
    '#title'       => t('Replacement patterns'),
    '#type'        => 'fieldset',
    '#collapsible' => TRUE,
    '#collapsed'   => TRUE,
  );
  $form['mail']['token_help']['help'] = array(
    '#value' => $doc,
  );
  $form['op'] = array(
    '#type'   => 'submit',
    '#value'  => $update ? t('Update template') : t('Save new template'),
    '#submit' => array('mail_edit_trans_save'),
  );
  if ($update) {
    $form['delete'] = array(
      '#type'   => 'submit',
      '#value'  => t('Remove @language translation', array('@language' => $lang)),
      '#submit' => array('mail_edit_trans_delete'),
    );
  }
  return $form;
}

function mail_edit_trans_delete($form, &$form_state) {
  $query = "DELETE FROM {mail_edit} WHERE id = '%s' AND language = '%s'";
  $args[] = $form_state['values']['id'];
  $args[] = $form_state['values']['language'];
  $variables = array('%language' => $form_state['values']['language'], '%id' => $form_state['values']['id']);
  if (db_query($query, $args)) {
    drupal_set_message(t('%language translation of %id has been removed', $variables));
  }
  else {
    drupal_set_message(t('Failed to remove %language translation of %id', $variables));
  }
  $form_state['redirect'] = 'admin/build/mail-edit';

}

function mail_edit_trans_save($form, &$form_state) {
  $update = $form_state['values']['update'];
  if ($update) {
    // Do update.
    $query = "
      UPDATE {mail_edit} SET
        description = '%s',
        subject     = '%s',
        body        = '%s'
      WHERE
        id     = '%s' AND
        language    = '%s'
    ";
    $values = $form_state['values'];
    $args[] = $values['description'];
    $args[] = $values['subject'];
    $args[] = $values['body'];
    $args[] = $values['id'];
    $args[] = $values['language'];
    if (db_query($query, $args)) {
    }
    else {
      drupal_set_message(t('Failed to save template for %id for the %language translation', array('%id' => $values['id'], '%language' => $values['language'])));
    }
  }
  else {
    // Do insert.
    $query = "INSERT INTO {mail_edit} (description, subject, id, body, language) VALUES (
      '%s',
      '%s',
      '%s',
      '%s',
      '%s'
    ) ";
    $values = $form_state['values'];
    $args[] = $values['description'];
    $args[] = $values['subject'];
    $args[] = $values['id'];
    $args[] = $values['body'];
    $args[] = $values['language'];
    if (!db_query($query, $args)) {
      drupal_set_message(t('Failed to save template for %id for the %language translation', array('%id' => $values['id'], '%language' => $values['language'])));
    }
  }
  $form_state['redirect'] = 'admin/build/mail-edit';
}

/**
 * Builds the registry of exposed mailkeys, keeps track of which module
 * exposed them.
 *
 * @return unknown
 */
function _mail_edit_key_registry_rebuild() {
  // Fetch exposed mailkeys.
  $hook = 'mailkeys';
  $mailkeys = array();
  foreach (module_implements($hook) as $module) {
    $function = $module .'_'. $hook;
    $result = $function();
    if (isset($result)) {
      $mailkeys[$module] = $result;
    }
  }
  // Find out if we already have record of the exposed keys.
  $query = "SELECT * FROM {mail_edit_registry}";
  $result = db_query($query);
  while ($row = db_fetch_object($result)) {
    if (isset($mailkeys[$row->module][$row->mailkey])) {
      unset($mailkeys[$row->module][$row->mailkey]);
    }
  }
  // Insert any new mailkeys into our registry table.
  foreach ($mailkeys as $module => $keys) {
    foreach ($keys as $key => $description) {
      $query = "INSERT INTO {mail_edit_registry} (id, module, mailkey, description) VALUES ('%s', '%s', '%s', '%s')";
      db_query($query, $module .'_'. $key, $module, $key, $description);
    }
  }
}

