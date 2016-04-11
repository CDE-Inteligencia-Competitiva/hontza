<?php

/**
 * @file
 * User page callback file for the user module.
 */

/**
 * Menu callback; Retrieve a JSON object containing autocomplete suggestions for existing users.
 */
function user_autocomplete($string = '') { 
  $matches = array();
  if ($string) {
    $result = db_query_range("SELECT name FROM {users} WHERE LOWER(name) LIKE LOWER('%s%%')", $string, 0, 10);
    while ($user = db_fetch_object($result)) {
      $matches[$user->name] = check_plain($user->name);
    }
  }
  
  drupal_json($matches);
}

/**
 * Form builder; Request a password reset.
 *
 * @ingroup forms
 * @see user_pass_validate()
 * @see user_pass_submit()
 */
function user_pass() {
  $form['name'] = array(
    '#type' => 'textfield',
    '#title' => t('Username or e-mail address'),
    '#size' => 60,
    '#maxlength' => max(USERNAME_MAX_LENGTH, EMAIL_MAX_LENGTH),
    '#required' => TRUE,
  );
  $form['submit'] = array('#type' => 'submit', '#value' => t('E-mail new password'));
  //intelsat-2015
  drupal_set_title(t('New Password'));
  //intelsat-2015
  if(red_crear_usuario_is_custom_css()){
      crear_usuario_add_submit_class($form);
  }
  return $form;
}

function user_pass_validate($form, &$form_state) {
  $name = trim($form_state['values']['name']);
  

  // Try to load by email.
  $account = user_load(array('mail' => $name, 'status' => 1));
  if (!$account) {
    // No success, try to load by name.
    $account = user_load(array('name' => $name, 'status' => 1));
  }
  if ($account) {
    // Blocked accounts cannot request a new password,
    // check provided username and email against access rules.
    if (drupal_is_denied('user', $account->name) || drupal_is_denied('mail', $account->mail)) {
      form_set_error('name', t('%name is not allowed to request a new password.', array('%name' => $name)));
    }
  }
  if (isset($account->uid)) {
    form_set_value(array('#parents' => array('account')), $account, $form_state);
  }
  else {
    form_set_error('name', t('Sorry, %name is not recognized as a user name or an e-mail address.', array('%name' => $name)));
  }
}

function user_pass_submit($form, &$form_state) {
  global $language;

  $account = $form_state['values']['account'];
  // Mail one time login URL and instructions using current language.
  _user_mail_notify('password_reset', $account, $language);
  watchdog('user', 'Password reset instructions mailed to %name at %email.', array('%name' => $account->name, '%email' => $account->mail));
  drupal_set_message(t('Further instructions have been sent to your e-mail address.'));

  $form_state['redirect'] = 'user';
  return;
}

/**
 * Menu callback; process one time login link and redirects to the user page on success.
 */
function user_pass_reset(&$form_state, $uid, $timestamp, $hashed_pass, $action = NULL) {
  global $user;

  // Check if the user is already logged in. The back button is often the culprit here.
  if ($user->uid) {
    drupal_set_message(t('You have already used this one-time login link. It is not necessary to use this link to login anymore. You are already logged in.'));
    drupal_goto();
  }
  else {
    // Time out, in seconds, until login URL expires. 24 hours = 86400 seconds.
    $timeout = 86400;
    $current = time();
    // Some redundant checks for extra security ?
    if ($timestamp < $current && $account = user_load(array('uid' => $uid, 'status' => 1)) ) {
      // Deny one-time login to blocked accounts.
      if (drupal_is_denied('user', $account->name) || drupal_is_denied('mail', $account->mail)) {
        drupal_set_message(t('You have tried to use a one-time login for an account which has been blocked.'), 'error');
        drupal_goto();
      }

      // No time out for first time login.
      if ($account->login && $current - $timestamp > $timeout) {
        drupal_set_message(t('You have tried to use a one-time login link that has expired. Please request a new one using the form below.'));
        drupal_goto('user/password');
      }
      else if ($account->uid && $timestamp > $account->login && $timestamp < $current && $hashed_pass == user_pass_rehash($account->pass, $timestamp, $account->login)) {
        // First stage is a confirmation form, then login
        if ($action == 'login') {
          watchdog('user', 'User %name used one-time login link at time %timestamp.', array('%name' => $account->name, '%timestamp' => $timestamp));
          // Set the new user.
          $user = $account;
          // user_authenticate_finalize() also updates the login timestamp of the
          // user, which invalidates further use of the one-time login link.
          user_authenticate_finalize($form_state['values']);
          drupal_set_message(t('You have just used your one-time login link. It is no longer necessary to use this link to login. Please change your password.'));
          drupal_goto('user/'. $user->uid .'/edit');
        }
        else {
          $form['message'] = array('#value' => t('<p>This is a one-time login for %user_name and will expire on %expiration_date.</p><p>Click on this button to login to the site and change your password.</p>', array('%user_name' => $account->name, '%expiration_date' => format_date($timestamp + $timeout))));
          $form['help'] = array('#value' => '<p>'. t('This login can be used only once.') .'</p>');
          $form['submit'] = array('#type' => 'submit', '#value' => t('Log in'));
          $form['#action'] = url("user/reset/$uid/$timestamp/$hashed_pass/login");
          return $form;
        }
      }
      else {
        drupal_set_message(t('You have tried to use a one-time login link which has either been used or is no longer valid. Please request a new one using the form below.'));
        drupal_goto('user/password');
      }
    }
    else {
      // Deny access, no more clues.
      // Everything will be in the watchdog's URL for the administrator to check.
      drupal_access_denied();
    }
  }
}

/**
 * Menu callback; logs the current user out, and redirects to the home page.
 */
function user_logout() {
  global $user;

  watchdog('user', 'Session closed for %name.', array('%name' => $user->name));

  //gemini-2014
  $is_iframe=0;
  if(red_is_iframe()){
      $is_iframe=1;
  }  
  // Destroy the current session:
  session_destroy();  
  // Only variables can be passed by reference workaround.
  $null = NULL;
  user_module_invoke('logout', $null, $user);

  // Load the anonymous user
  $user = drupal_anonymous_user();
  
  //gemini-2014
  if(!empty($is_iframe)){
    global $base_url;
    drupal_goto($base_url.'/index.php?is_iframe=1');
  }else{    
    drupal_goto();
  }
}

/**
 * Menu callback; Displays a user or user profile page.
 */
function user_view($account) {
  drupal_set_title(check_plain($account->name));
  // Retrieve all profile fields and attach to $account->content.
  user_build_content($account);

  // To theme user profiles, copy modules/user/user_profile.tpl.php
  // to your theme directory, and edit it as instructed in that file's comments.
  //intelsat-2015
  if(hontza_canal_rss_is_facilitador_activado()){
   return facilitador_facilitador_view($account);
  }else{
  //    
    return theme('user_profile', $account);
  }  
}

/**
 * Process variables for user-profile.tpl.php.
 *
 * The $variables array contains the following arguments:
 * - $account
 *
 * @see user-picture.tpl.php
 */
function template_preprocess_user_profile(&$variables) {
  $variables['profile'] = array();
  // Sort sections by weight
  uasort($variables['account']->content, 'element_sort');
  // Provide keyed variables so themers can print each section independantly.
  foreach (element_children($variables['account']->content) as $key) {
    $variables['profile'][$key] = drupal_render($variables['account']->content[$key]);
  }
  // Collect all profiles to make it easier to print all items at once.
  $variables['user_profile'] = implode($variables['profile']);
}

/**
 * Process variables for user-profile-item.tpl.php.
 *
 * The $variables array contains the following arguments:
 * - $element
 *
 * @see user-profile-item.tpl.php
 */
function template_preprocess_user_profile_item(&$variables) {
  $variables['title'] = $variables['element']['#title'];
  $variables['value'] = $variables['element']['#value'];
  $variables['attributes'] = '';
  if (isset($variables['element']['#attributes'])) {
    $variables['attributes'] = drupal_attributes($variables['element']['#attributes']);
  }
}

/**
 * Process variables for user-profile-category.tpl.php.
 *
 * The $variables array contains the following arguments:
 * - $element
 *
 * @see user-profile-category.tpl.php
 */
function template_preprocess_user_profile_category(&$variables) {
  $variables['title'] = check_plain($variables['element']['#title']);
  $variables['profile_items'] = $variables['element']['#children'];
  $variables['attributes'] = '';
  if (isset($variables['element']['#attributes'])) {
    $variables['attributes'] = drupal_attributes($variables['element']['#attributes']);
  }
}

/**
 * Form builder; Present the form to edit a given user or profile category.
 *
 * @ingroup forms
 * @see user_edit_validate()
 * @see user_edit_submit()
 */
function user_edit($account, $category = 'account') {
  drupal_set_title(check_plain($account->name));
  return drupal_get_form('user_profile_form', $account, $category);
}

/**
 * Form builder; edit a user account or one of their profile categories.
 *
 * @ingroup forms
 * @see user_profile_form_validate()
 * @see user_profile_form_submit()
 * @see user_edit_delete_submit()
 */
function user_profile_form($form_state, $account, $category = 'account') {
    
  $edit = (empty($form_state['values'])) ? (array)$account : $form_state['values'];

  $form = _user_forms($edit, $account, $category);
  //intelsat-2015
  user_add_profile_fields($form,$account, $category,$edit);
  //
  $form['_category'] = array('#type' => 'value', '#value' => $category);
  $form['_account'] = array('#type' => 'value', '#value' => $account);
  $form['submit'] = array('#type' => 'submit', '#value' => t('Save'), '#weight' => 30);
  //intelsat-2015
  if(red_crear_usuario_is_custom_css()){
      crear_usuario_add_submit_class($form);
  }
  if (user_access('administer users')) {
    $form['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete'),
      '#weight' => 31,
      '#submit' => array('user_edit_delete_submit'),
    );
  }
  $form['#attributes']['enctype'] = 'multipart/form-data';

  return $form;
}

/**
 * Validation function for the user account and profile editing form.
 */
function user_profile_form_validate($form, &$form_state) {
  user_module_invoke('validate', $form_state['values'], $form_state['values']['_account'], $form_state['values']['_category']);
  //gemini-2013
  if(hontza_is_claves()){
      claves_user_pass_validate($form,$form_state);
  }
  // Validate input to ensure that non-privileged users can't alter protected data.
  if ((!user_access('administer users') && array_intersect(array_keys($form_state['values']), array('uid', 'init', 'session'))) || (!user_access('administer permissions') && isset($form_state['values']['roles']))) {
    //gemini-2014
    if(!hontza_user_access_gestion_usuarios()){
        watchdog('security', 'Detected malicious attempt to alter protected user fields.', array(), WATCHDOG_WARNING);
        // set this to a value type field
        form_set_error('category', t('Detected malicious attempt to alter protected user fields.'));
    }    
  }
  //intelsat-2015
  if(red_is_validar_usuario_network_activado()){
      if(isset($form_state['values']['_category']) && !empty($form_state['values']['_category']) && $form_state['values']['_category']=='account'){
        red_validar_usuario_network($form_state['values']);
      }  
  }
}

/**
 * Submit function for the user account and profile editing form.
 */
function user_profile_form_submit($form, &$form_state) {
  $account = $form_state['values']['_account'];
  $category = $form_state['values']['_category'];  
  unset($form_state['values']['_account'], $form_state['values']['op'], $form_state['values']['submit'], $form_state['values']['delete'], $form_state['values']['form_token'], $form_state['values']['form_id'], $form_state['values']['_category']);
  user_module_invoke('submit', $form_state['values'], $account, $category);
  //gemini-2013
  $account_bakup=$account;
  $values_bakup=$form_state['values'];  
  user_save($account, $form_state['values'], $category);
  //gemini-2013
  if(hontza_is_claves()){
      claves_save_users_pass_by_edit($account_bakup,$values_bakup);
  }
  
  //intelsat-2016
  user_facilitator_save($values_bakup,$account,$category);
  
  //gemini-2014
  if(hontza_is_sareko_id_red()){
      if(isset($account->uid) && !empty($account->uid)){
        $my_user=user_load($account->uid);
        if(isset($my_user->uid) && !empty($my_user->uid)){
            red_compartir_grupo_on_user_save($my_user);
        }    
      }  
  }
  /*
  //intelsat-2015
  user_facilitator_save($values_bakup,$account,$category);*/
  //
  // Clear the page cache because pages can contain usernames and/or profile information:
  cache_clear_all();

  drupal_set_message(t('The changes have been saved.'));
  //intelsat-2015
  if(red_crear_usuario_is_activado()){
      crear_usuario_on_user_profile_form_submit();
  }
  //intelsat-2016
  hontza_registrar_on_user_profile_form_submit($category,$account);
  return;
}

/**
 * Submit function for the 'Delete' button on the user edit form.
 */
function user_edit_delete_submit($form, &$form_state) {
  $destination = '';
  if (isset($_REQUEST['destination'])) {
    $destination = drupal_get_destination();
    unset($_REQUEST['destination']);
  }
  // Note: We redirect from user/uid/edit to user/uid/delete to make the tabs disappear.
  $form_state['redirect'] = array("user/". $form_state['values']['_account']->uid ."/delete", $destination);
}

/**
 * Form builder; confirm form for user deletion.
 *
 * @ingroup forms
 * @see user_confirm_delete_submit()
 */
function user_confirm_delete(&$form_state, $account) {

  $form['_account'] = array('#type' => 'value', '#value' => $account);

  return confirm_form($form,
    t('Are you sure you want to delete the account %name?', array('%name' => $account->name)),
    'user/'. $account->uid,
    t('All submissions made by this user will be attributed to the anonymous account. This action cannot be undone.'),
    t('Delete'), t('Cancel'));
}

/**
 * Submit function for the confirm form for user deletion.
 */
function user_confirm_delete_submit($form, &$form_state) {
  user_delete($form_state['values'], $form_state['values']['_account']->uid);
  drupal_set_message(t('%name has been deleted.', array('%name' => $form_state['values']['_account']->name)));

  if (!isset($_REQUEST['destination'])) {
    $form_state['redirect'] = 'admin/user/user';
  }
}

function user_edit_validate($form, &$form_state) {
  user_module_invoke('validate', $form_state['values'], $form_state['values']['_account'], $form_state['values']['_category']);
  // Validate input to ensure that non-privileged users can't alter protected data.
  if ((!user_access('administer users') && array_intersect(array_keys($form_state['values']), array('uid', 'init', 'session'))) || (!user_access('administer permissions') && isset($form_state['values']['roles']))) {
    watchdog('security', 'Detected malicious attempt to alter protected user fields.', array(), WATCHDOG_WARNING);
    // set this to a value type field
    form_set_error('category', t('Detected malicious attempt to alter protected user fields.'));
  }
}

function user_edit_submit($form, &$form_state) {
  $account = $form_state['values']['_account'];
  $category = $form_state['values']['_category'];
  unset($form_state['values']['_account'], $form_state['values']['op'], $form_state['values']['submit'], $form_state['values']['delete'], $form_state['values']['form_token'], $form_state['values']['form_id'], $form_state['values']['_category']);
  user_module_invoke('submit', $form_state['values'], $account, $category);
    
  user_save($account, $form_state['values'], $category);

  // Clear the page cache because pages can contain usernames and/or profile information:
  cache_clear_all();

  drupal_set_message(t('The changes have been saved.'));
  return;
}

/**
 * Access callback for path /user.
 *
 * Displays user profile if user is logged in, or login form for anonymous
 * users.
 */
function user_page() {
  global $user;
  if ($user->uid) {
    menu_set_active_item('user/'. $user->uid);
    return menu_execute_active_handler();
  }
  else {
    return drupal_get_form('user_login');
  }
}
//gemini-2014
function user_pagina_de_arranque_edit_callback(){
    //return 'Funcion desactivada';
    drupal_set_title(t('Start page'));
    return drupal_get_form('user_pagina_de_arranque_form');
}
//gemini-2014
function user_pagina_de_arranque_form(){
    /*$default_group_nid='';
    $default_page='';
    $row=user_pagina_de_arranque_get_row();
    if(isset($row->id) && !empty($row->id)){
        $default_group_nid=$row->group_nid;
        $default_page=$row->start_page;
    }
    $form=array();
    $grupo_options=my_get_grupo_seguimiento_options(0);
    //simulando
    //$default_group_nid='';
    //
    if(empty($default_group_nid)){
        $group_keys=array_keys($grupo_options);
        if(isset($group_keys[0])){
            $default_group_nid=$group_keys[0];
        }
    }
    $page_options=user_get_pagina_de_arranque_pagina_options($default_group_nid);*/
    red_crear_usuario_get_pagina_arranque_values($grupo_options,$default_group_nid,$page_options,$default_page);
    $form['group_nid']=array(
        '#type'=>'select',
        '#title'=>t('Group'),
        '#options'=>$grupo_options,
        '#required'=>TRUE,
        '#default_value'=>$default_group_nid,
    );
    $form['page']=array(
        '#type'=>'select',
        '#title'=>t('Page'),
        '#options'=>$page_options,
        '#required'=>TRUE,
        '#default_value'=>$default_page,
    );
    $form['submit_btn']=array(
        '#type'=>'submit',
        '#value'=>t('Save'),
        '#name'=>'start_page_save_btn',
    );
    $form['cancel_btn']=array(
        '#value'=>l(t('Return'),'user'),
    );
    user_pagina_de_arranque_drupal_add_js();
    return $form;
}
//gemini-2014
function user_get_pagina_de_arranque_pagina_options($group_nid=''){
    $result=array();
    //$result['']='';
    $result['dashboard']=t('Home');
    $result['estrategias/arbol_estrategico']=t('Strategy');
    $result['fuentes-pipes/todas']=t('Sources');
    $result['vigilancia/pendientes']=t('Monitoring');
    $result['my_doc_list']=t('Docs');
    $result['area-debate']=t('Discussion');
    $result['area-trabajo']=t('Collaboration');
    $result['ideas']=t('Proposals');
    $result['usuarios_acceso/todos']=t('Group');
    $result['servicios']=t('Experts');
    //
    if(!empty($group_nid)){
        $options=array();
        foreach($result as $key=>$value){
                if(hontza_grupos_is_url_menutop_active($key,$group_nid)){
                    $options[$key]=$value;
                }
        }
        return $options;
    }
    return $result;
}
//gemini-2014
function user_pagina_de_arranque_form_submit($form,&$form_state){
    if(isset($form_state['clicked_button']) && !empty($form_state['clicked_button'])){
        if($form_state['clicked_button']['#name']=='start_page_save_btn'){
            $values=$form_state['values'];
            $group_nid=$values['group_nid'];
            $page=$values['page'];
            user_pagina_de_arranque_save($group_nid,$page);    
        }
    }    
}
//gemini-2014
function user_pagina_de_arranque_save($group_nid,$page){
    global $user;
    $row=user_pagina_de_arranque_get_row();
    if(isset($row->id) && !empty($row->id)){
        db_query('UPDATE {users_start_page} SET group_nid=%d,start_page="%s" WHERE uid=%d',$group_nid,$page,$user->uid);
    }else{
        db_query('INSERT INTO {users_start_page}(uid,group_nid,start_page) VALUES(%d,%d,"%s")',$user->uid,$group_nid,$page);
    }
}
//gemini-2014
//function user_pagina_de_arranque_get_row(){
//intelsat-2015
function user_pagina_de_arranque_get_row($uid_in=''){
    global $user;
    //intelsat-2015
    if(!empty($uid_in)){
        $uid=$uid_in;
    }else{
        $uid=$user->uid;
    }
    $res=db_query('SELECT * FROM {users_start_page} WHERE uid='.$uid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
//gemini-2014
function user_pagina_de_arranque_drupal_add_js(){
    global $base_url;
    $js='$(document).ready(function()
    {
        $("#edit-group-nid").unbind("change");
        $("#edit-group-nid").bind("change",function(){
            var group_nid=$(this).attr("value");
                jQuery.ajax({
				type: "POST",
				url: "'.$base_url.'/user/pagina_de_arranque_get_paginas_activas_ajax?my_time="+new Date().getTime(),
				data: {group_nid:group_nid},
				dataType:"json",
				success: function(my_result){
                                    $("#edit-page").children().remove();
                                    for(var page in my_result.pages){
                                        var option = document.createElement("option");
                                        option.text = my_result.pages[page].label;
                                        option.value = my_result.pages[page].value;
                                        $("#edit-page").append(option);                                            
                                    }
				}
			});
        });
    });';
    drupal_add_js($js,'inline');
}
//gemini-2014
function user_pagina_de_arranque_get_paginas_activas_ajax_callback(){
    $result=array();
    $result['ok']=1;
    $pages=array();
    if(isset($_POST['group_nid']) && !empty($_POST['group_nid'])){
        $group_nid=$_POST['group_nid'];
        $options=user_get_pagina_de_arranque_pagina_options();
        if(!empty($options)){
            foreach($options as $key=>$value){
                if(hontza_grupos_is_url_menutop_active($key,$group_nid)){
                    $r=new stdClass();
                    $r->label=$value;
                    $r->value=$key;
                    $pages[]=$r;
                }
            }
        }
    }
    $result['pages']=$pages;
    print json_encode($result);
    exit();
}
function user_add_account_tab_fields(&$form,$account, $edit){
    $form_datos_personales=_user_forms($edit, $account,'Datos Personales');
    $form_datos_personales['Datos personales']['#prefix']='<div style="clear:both;">';
    $form_datos_personales['Datos personales']['#suffix']='</div>';
    $form_datos_personales['Datos personales']['#weight']=-6;
    $form=array_merge($form,$form_datos_personales);
}
function user_add_profile_fields(&$form,$account, $category,$edit){
    if($category=='account'){
      user_add_account_tab_fields($form,$account, $edit);
      user_add_internet_profiles_tab_fields($form,$account, $edit);
    }else if($category=='Consultoria_en_gestion_de_la_informacion'){
      user_add_facilitator_select_js($edit);  
      user_add_facilitator_tab_fields($form,$account, $edit);
      $form['Consultoria_en_gestion_de_la_informacion']['#attributes']['id']='id_Consultoria_en_gestion_de_la_informacion';
      user_add_innovation_consultancy_tab_fields($form,$account, $edit);
      user_add_strategic_consultancy_tab_fields($form,$account, $edit);
      user_add_icts_optimisation_tab_fields($form,$account, $edit);
      user_add_services_tab_fields($form,$account, $edit);
    }    
}
function user_add_internet_profiles_tab_fields(&$form,$account, $edit){
    $form_internet_profiles=_user_forms($edit, $account,'Perfiles_web');
    $form_internet_profiles['Perfiles_web']['#weight']=-6;
    $form=array_merge($form,$form_internet_profiles);
}
function user_add_facilitator_tab_fields(&$form,$account, $edit){
    $form['facilitator_fieldset']=array(
        '#type'=>'fieldset',
        //intelsat-2016
        //'#title'=>t('Facilitator'),
        '#title'=>t('Expert'),
    );
    $is_facilitator=user_get_is_facilitador_value($account->uid);
    $form['facilitator_fieldset']['is_facilitator']=array(
        '#type'=>'checkbox',
        //intelsat-2016
        //'#title'=>t('Facilitator'),
        '#title'=>t('Expert'),
    );
    if($is_facilitator){
        $form['facilitator_fieldset']['is_facilitator']['#attributes']=array('checked'=>'checked');
    }
    //intelsat-2015
    if(hontza_canal_rss_is_facilitador_activado()){
        facilitador_user_add_facilitator_tab_fields($form,$account, $edit);
    }
    //
}
function user_add_innovation_consultancy_tab_fields(&$form,$account, $edit){    
    $form_innovation_consultancy=_user_forms($edit, $account,'Consultoria_en_innovacion');
    $form_innovation_consultancy['Consultoria_en_innovacion']['#weight']=2;
    $form_innovation_consultancy['Consultoria_en_innovacion']['#attributes']['id']='id_Consultoria_en_innovacion';
    $form=array_merge($form,$form_innovation_consultancy);
}
function user_add_strategic_consultancy_tab_fields(&$form,$account, $edit){
    $form_strategic_consultancy=_user_forms($edit, $account,'Consultoria_estrategica');
    $form_strategic_consultancy['Consultoria_estrategica']['#weight']=3;
    $form_strategic_consultancy['Consultoria_estrategica']['#attributes']['id']='id_Consultoria_estrategica';
    $form=array_merge($form,$form_strategic_consultancy);
}
function user_add_icts_optimisation_tab_fields(&$form,$account, $edit){
    $form_icts_optimisation=_user_forms($edit, $account,'Optimizacion_tics');
    $form_icts_optimisation['Optimizacion_tics']['#weight']=4;
    $form_icts_optimisation['Optimizacion_tics']['#attributes']['id']='id_Optimizacion_tics';
    $form=array_merge($form,$form_icts_optimisation);
}
function user_add_services_tab_fields(&$form,$account, $edit){
    $form_services=_user_forms($edit, $account,'Servicios');
    $form_services['Servicios']['#weight']=5;
    $form_services['Servicios']['#attributes']['id']='id_Servicios';
    $form=array_merge($form,$form_services);
}
//intelsat-2015
function user_facilitator_save($form_state_values,$account,$category){
    if(hontza_canal_rss_is_facilitador_activado()){
        facilitator_save($form_state_values,$account,$category);
    }
}
//intelsat-2015
function user_get_is_facilitador_value($uid){
    if(hontza_canal_rss_is_facilitador_activado()){
        return facilitador_get_is_facilitador_value($uid);
    }
    return 0;
}
//gemini-2014
function user_add_facilitator_select_js($edit){
    if(hontza_canal_rss_is_facilitador_activado()){
        facilitador_add_facilitator_select_js($edit);
    }
}
//gemini
function user_custom_delete_callback(){
    drupal_goto('user/'.arg(1).'/delete');
}