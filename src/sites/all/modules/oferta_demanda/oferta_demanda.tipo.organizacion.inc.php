<?php
require_once('sites/all/modules/taxonomy/taxonomy.admin.inc');
function oferta_demanda_tipo_organizacion_get_tipos_organizacion_title(){
    return t('Types of Organisations');
}
function oferta_demanda_tipo_organizacion_get_tipos_organizacion_block_content(){
    if(oferta_demanda_clasificacion_edit_access()){
        $html=array();    
        $add_label=t('Add Type of Organisation');
        $icono=my_get_icono_action('add_left',$add_label).'&nbsp;';
        $html[]=l($icono.$add_label,'oferta_demanda/tipo_organizacion/add/term',array('query'=>'destination=oferta_demanda/tipos_organizacion','html'=>true));   
        $title=oferta_demanda_tipo_organizacion_get_tipos_organizacion_title();
        $html[]=l($title,'oferta_demanda/tipos_organizacion');
        return implode('<BR>',$html);
    }    
    return '';    
}
function oferta_demanda_tipo_organizacion_tipos_organizacion_callback(){
    $vocabulary=oferta_demanda_tipo_organizacion_vocabulary_load();
    $result=drupal_get_form('taxonomy_overview_terms',$vocabulary);
    drupal_set_title(oferta_demanda_tipo_organizacion_get_tipos_organizacion_title());
    return $result;
}
function oferta_demanda_tipo_organizacion_edit_callback(){
   $tid=arg(2);
   if ($term = (array)taxonomy_get_term($tid)) {
    //gemini-2014
    taxonomy_my_set_edit_term_title($tid);  
    return drupal_get_form('taxonomy_form_term', taxonomy_vocabulary_load($term['vid']), $term);
   }
   return drupal_not_found();
}
function oferta_demanda_tipo_organizacion_add_term_callback(){
  $vocabulary=oferta_demanda_tipo_organizacion_vocabulary_load();  
  taxonomy_my_set_add_term_page_title($vocabulary);
  return drupal_get_form('oferta_demanda_tipo_organizacion_taxonomy_form_term' , $vocabulary); 
}
function oferta_demanda_tipo_organizacion_vocabulary_load(){
    $vid=25;
    $vocabulary=taxonomy_vocabulary_load($vid);
    return $vocabulary;
}
function oferta_demanda_tipo_organizacion_tipos_organizacion_temp_callback(){
    drupal_goto('oferta_demanda/tipos_organizacion');
}
function oferta_demanda_tipo_organizacion_taxonomy_form_term(&$form_state, $vocabulary, $edit = array()) {
  $edit += array(
    'name' => '',
    'description' => '',
    'tid' => NULL,
    'weight' => 0,
  );

  $parent = array_keys(taxonomy_get_parents($edit['tid']));
  $form['#term'] = $edit;
  $form['#term']['parent'] = $parent;
  $form['#vocabulary'] = (array)$vocabulary;
  $form['#vocabulary']['nodes'] = drupal_map_assoc($vocabulary->nodes);;

  // Check for confirmation forms.
  if (isset($form_state['confirm_delete'])) {
    return array_merge($form, taxonomy_term_confirm_delete($form_state, $edit['tid']));
  }
  elseif (isset($form_state['confirm_parents'])) {
    return array_merge($form, taxonomy_term_confirm_parents($form_state, $vocabulary));
  }

  $form['identification'] = array(
    '#type' => 'fieldset',
    '#title' => t('Identification'),
    '#collapsible' => TRUE,
  );
  $form['identification']['name'] = array(
    '#type' => 'textfield',
    //gemini-2014
    //'#title' => t('Term name'),
    '#title' => taxonomy_my_set_identification_name_title($vocabulary),  
    '#default_value' => $edit['name'],
    '#maxlength' => 255,
    '#description' => t('The name of this term.'),
    '#required' => TRUE);
  $form['identification']['description'] = array(
    '#type' => 'textarea',
    '#title' => t('Description'),
    '#default_value' => $edit['description'],
    '#description' => t('A description of the term. To be displayed on taxonomy/term pages and RSS feeds.'));


  //gemini
  //if(is_vocabulary_translate($vocabulary)){
  $form['my_translation']=create_my_translation_term($edit,$vocabulary);
  //}
  //

  $form['advanced'] = array(
    '#type' => 'fieldset',
    '#title' => t('Advanced options'),
    '#collapsible' => TRUE,
    '#collapsed' => $vocabulary->hierarchy > 1 ? FALSE : TRUE,
  );

  // taxonomy_get_tree and taxonomy_get_parents may contain large numbers of
  // items so we check for taxonomy_override_selector before loading the
  // full vocabulary. Contrib modules can then intercept before
  // hook_form_alter to provide scalable alternatives.
  if (!variable_get('taxonomy_override_selector', FALSE)) {
    $parent = array_keys(taxonomy_get_parents($edit['tid']));
    $children = taxonomy_get_tree($vocabulary->vid, $edit['tid']);

    // A term can't be the child of itself, nor of its children.
    foreach ($children as $child) {
      $exclude[] = $child->tid;
    }
    $exclude[] = $edit['tid'];

    $form['advanced']['parent'] = _taxonomy_term_select(t('Parents'), 'parent', $parent, $vocabulary->vid, t('Parent terms') .'.', 1, '<'. t('root') .'>', $exclude);
    $form['advanced']['relations'] = _taxonomy_term_select(t('Related terms'), 'relations', array_keys(taxonomy_get_related($edit['tid'])), $vocabulary->vid, NULL, 1, '<'. t('none') .'>', array($edit['tid']));
  }
  $form['advanced']['synonyms'] = array(
    '#type' => 'textarea',
    '#title' => t('Synonyms'),
    '#default_value' => implode("\n", taxonomy_get_synonyms($edit['tid'])),
    '#description' => t('Synonyms of this term, one synonym per line.'));
  $form['advanced']['weight'] = array(
    '#type' => 'textfield',
    '#title' => t('Weight'),
    '#size' => 6,
    '#default_value' => $edit['weight'],
    '#description' => t('Terms are displayed in ascending order by weight.'),
    '#required' => TRUE);
  $form['vid'] = array(
    '#type' => 'value',
    '#value' => $vocabulary->vid);
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'));

  if ($edit['tid']) {
    $form['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete'));
    $form['tid'] = array(
      '#type' => 'value',
      '#value' => $edit['tid']);
  }
  else {
    $form['destination'] = array('#type' => 'hidden', '#value' => $_GET['q']);
  }  
  //intelsat-2015
  if($vocabulary->vid==25){
      $title=t('Add Type of Organisation');        
  }else if($vocabulary->vid==26){
      $title=t('Add Role');  
  }else if($vocabulary->vid==27){
      $title=t('Add Technological Area');  
  }else if($vocabulary->vid==28){
      $title=t('Add Activity sector');  
  }
  drupal_set_title($title);
  //  
  return $form;
}

/**
 * Validation handler for the term edit form. Ensure numeric weight values.
 *
 * @see taxonomy_form_term()
 */
function oferta_demanda_tipo_organizacion_taxonomy_form_term_validate($form, &$form_state) {
  if (isset($form_state['values']['weight']) && !is_numeric($form_state['values']['weight'])) {
    form_set_error('weight', t('Weight value must be numeric.'));
  }
}

/**
 * Submit handler to insert or update a term.
 *
 * @see taxonomy_form_term()
 */
function oferta_demanda_tipo_organizacion_taxonomy_form_term_submit($form, &$form_state) {
  if ($form_state['clicked_button']['#value'] == t('Delete')) {
    // Execute the term deletion.
    if ($form_state['values']['delete'] === TRUE) {
      return taxonomy_term_confirm_delete_submit($form, $form_state);
    }
    // Rebuild the form to confirm term deletion.
    $form_state['rebuild'] = TRUE;
    $form_state['confirm_delete'] = TRUE;
    return;
  }
  // Rebuild the form to confirm enabling multiple parents.
  elseif ($form_state['clicked_button']['#value'] == t('Save') && !$form['#vocabulary']['tags'] && count($form_state['values']['parent']) > 1 && $form['#vocabulary']['hierarchy'] < 2) {
    $form_state['rebuild'] = TRUE;
    $form_state['confirm_parents'] = TRUE;
    return;
  }

  switch (taxonomy_save_term($form_state['values'])) {
    
    case SAVED_NEW:
      //gemini
      my_save_term_extra($form_state['values']);
      //  
      drupal_set_message(t('Created new term %term.', array('%term' => $form_state['values']['name'])));
      watchdog('taxonomy', 'Created new term %term.', array('%term' => $form_state['values']['name']), WATCHDOG_NOTICE, l(t('edit'), 'admin/content/taxonomy/edit/term/'. $form_state['values']['tid']));
      break;
    case SAVED_UPDATED:
      //gemini
      my_save_term_extra($form_state['values']);
      //
      drupal_set_message(t('Updated term %term.', array('%term' => $form_state['values']['name'])));
      watchdog('taxonomy', 'Updated term %term.', array('%term' => $form_state['values']['name']), WATCHDOG_NOTICE, l(t('edit'), 'admin/content/taxonomy/edit/term/'. $form_state['values']['tid']));
      break;
  }

  if (!$form['#vocabulary']['tags']) {
    $current_parent_count = count($form_state['values']['parent']);
    $previous_parent_count = count($form['#term']['parent']);
    // Root doesn't count if it's the only parent.
    if ($current_parent_count == 1 && isset($form_state['values']['parent'][''])) {
      $current_parent_count = 0;
      $form_state['values']['parent'] = array();
    }

    // If the number of parents has been reduced to one or none, do a check on the
    // parents of every term in the vocabulary value.
    if ($current_parent_count < $previous_parent_count && $current_parent_count < 2) {
      taxonomy_check_vocabulary_hierarchy($form['#vocabulary'], $form_state['values']);
    }
    // If we've increased the number of parents and this is a single or taxonomy_get_terflat
    // hierarchy, update the vocabulary immediately.
    elseif ($current_parent_count > $previous_parent_count && $form['#vocabulary']['hierarchy'] < 2) {
      $form['#vocabulary']['hierarchy'] = $current_parent_count == 1 ? 1 : 2;
      taxonomy_save_vocabulary($form['#vocabulary']);
    }
  }

  $form_state['tid'] = $form_state['values']['tid'];
  $form_state['redirect'] = 'admin/content/taxonomy';
  $url=oferta_demanda_get_url_clasificacion_terminos('admin/content/taxonomy',1);
  $url=url($url);
  header('Location:'.$url);
  exit();
  return;
}
function oferta_demanda_tipo_organizacion_delete_callback(){
  $tid=arg(2);
  if ($term = (array)taxonomy_get_term($tid)) {
    return drupal_get_form('taxonomy_additions_form_term_delete', taxonomy_vocabulary_load($term['vid']), $term);
  }
  return drupal_not_found();  
}