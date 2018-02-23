<?php
// $Id: taxonomy_additions_plugin_argument_validate_taxonomy_term_related.inc,v 1.1 2011/02/01 00:20:14 amateescu Exp $

/**
 * @file
 * Contains the 'taxonomy term related' argument validator plugin.
 */

/**
 * Validate whether an argument is an acceptable node.
 */
class taxonomy_additions_plugin_argument_validate_taxonomy_term_related extends views_plugin_argument_validate {
  function validate_form(&$form, &$form_state) {
    $vocabularies = taxonomy_get_vocabularies();
    $options = array();
    foreach ($vocabularies as $voc) {
      $options[$voc->vid] = check_plain($voc->name);
    }

    $form['validate_argument_vocabulary'] = array(
      '#type' => 'checkboxes',
      '#prefix' => '<div id="edit-options-validate-argument-vocabulary-wrapper">',
      '#suffix' => '</div>',
      '#title' => t('Categories'),
      '#options' => $options,
      '#default_value' => isset($this->argument->options['validate_argument_vocabulary']) ? $this->argument->options['validate_argument_vocabulary'] : array(),
      '#description' => t('If you wish to validate for specific vocabularies, check them; if none are checked, all terms will pass.'),
      '#process' => array('expand_checkboxes', 'views_process_dependency'),
      '#dependency' => array('edit-options-validate-type' => array($this->id)),
    );
  }

  function validate_argument($argument) {
    $list = array($argument);
    $vids = isset($this->argument->options['validate_argument_vocabulary']) ? array_filter($this->argument->options['validate_argument_vocabulary']) : array();

    if (!is_numeric($argument)) {
      return FALSE;
    }

    $result = db_fetch_object(db_query("SELECT * FROM {term_data} WHERE tid = %d", $argument));
    if (!$result) {
      return FALSE;
    }

    foreach (taxonomy_get_related($argument) as $term) {
      if (empty($vids) || (!empty($vids) && in_array($term->vid, $vids))) {
        $list[] = $term->tid;
      }
    }
    
    $this->argument->argument = implode('+', $list);
    $this->argument->validated_title = check_plain($result->name);

    return empty($vids) || !empty($vids[$result->vid]);
  }
}
