<?php
function hontza_admin_settings(){
  drupal_set_title(t('Settings - Public/Highlighted News'));
  $form['home_noticias_publicas_num'] = array(
    '#type' => 'textfield',
    '#title' => t('Number of public news'),
    '#default_value' => variable_get('home_noticias_publicas_num',100),
    '#size'=>10,
    '#maxlength'=>10,
    '#required' => TRUE,
    //intelsat-2015
    '#description'=>t('Public news will be published in Hontza home page'),  
    //  
  );

  $form['home_noticias_destacadas_num'] = array(
    '#type' => 'textfield',
    '#title' => t('Number of highlighted news'),
    '#default_value' => variable_get('home_noticias_destacadas_num',100),
    '#size'=>10,
    '#maxlength'=>10,
    '#required' => TRUE,
    //intelsat-2015
    '#description'=>t('Highlighted news will be published in Hontza home page. Also they will be available to be included in Alerts and Bulletins'),
    //  
  );

  $form['#validate'][] = 'my_hontza_admin_settings_validate';
  return system_settings_form($form);
}
function my_hontza_admin_settings_validate(&$form, &$form_state){
  $fields=array('home_noticias_publicas_num'=>'Number of public news','home_noticias_destacadas_num'=>'Number of highlighted news');
  //
   validate_numeric_fields($form, $form_state,$fields);
}
function hontza_my_idea_settings(){
  drupal_set_title(t('Promotion Criteria'));
  $form['promocionar_idea_min'] = array(
    '#type' => 'textfield',
    '#title' => t('Minimum number of supporters to Promote Idea to Opportunity'),
    '#default_value' => variable_get('promocionar_idea_min',2),
    '#size'=>10,
    '#maxlength'=>10,
    '#required' => TRUE,
  );

  $form['promocionar_oportunidad_min'] = array(
    '#type' => 'textfield',
    '#title' => t('Minimum number of supporters to Promote Opportunity to Project'),
    '#default_value' => variable_get('promocionar_oportunidad_min',3),
    '#size'=>10,
    '#maxlength'=>10,
    '#required' => TRUE,
  );

  $form['#validate'][] = 'hontza_my_idea_settings_validate';
  return system_settings_form($form);
}
function hontza_my_idea_settings_validate(&$form, &$form_state){
  //$fields=array('promocionar_idea_min'=>'Promocionar Idea min','promocionar_oportunidad_min'=>'Promocionar Oportunidad min');
  $fields=array('promocionar_idea_min'=>'Minimum number of supporters to Promote Idea to Opportunity','promocionar_oportunidad_min'=>'Minimum number of supporters to Promote Opportunity to Project'); 
 //
  validate_numeric_fields($form, $form_state,$fields);
}
function validate_numeric_fields(&$form, &$form_state,$fields){
  idea_validate_numeric_fields($form, $form_state, $fields);
}
function hontza_post_formulario_settings_form(){
  drupal_set_title(t('Welcome Messages'));
  $form['frase_post_probar_hontza_online'] = array(
    '#type' => 'textarea',
    '#title' => t('Try Hontza Online'),
    '#default_value' => variable_get('frase_post_probar_hontza_online',''),
    /*'#size'=>10,
    '#maxlength'=>10,
    '#required' => TRUE,*/
  );

  $form['frase_post_consultores_en_inteligencia_competitiva'] = array(
    '#type' => 'textarea',
    '#title' => t('Competitive Intelligence Consultants'),
    '#default_value' => variable_get('frase_post_consultores_en_inteligencia_competitiva',''),
    /*'#size'=>10,
    '#maxlength'=>10,
    '#required' => TRUE,*/
  );

  $form['frase_post_facilitador'] = array(
    '#type' => 'textarea',
    '#title' => t('Expert'),
    '#default_value' => variable_get('frase_post_facilitador',''),
    /*'#size'=>10,
    '#maxlength'=>10,
    '#required' => TRUE,*/
  );

  //$form['#validate'][] = 'hontza_my_idea_settings_validate';
  return system_settings_form($form);
}
function hontza_admin_email_settings() {
  $form = array();

  $form['my_from_default_mail'] = array(
    '#title' => t('No reply email'),
    '#type' => 'textfield',
    '#default_value' => variable_get('my_from_default_mail', 'no-reply@hontza.es'),
    '#required' => TRUE
  );
  
  //intelsat-2015
  drupal_set_title(t('No reply email'));  
   return system_settings_form($form);
}