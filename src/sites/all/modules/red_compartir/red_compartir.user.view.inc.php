<?php
function red_compartir_user_view($account) {
  drupal_set_title(check_plain($account->name));
  // Retrieve all profile fields and attach to $account->content.
  //gemini-2014
  red_compartir_user_build_content($account);

  // To theme user profiles, copy modules/user/user_profile.tpl.php
  // to your theme directory, and edit it as instructed in that file's comments.
  return theme('user_profile', $account);
}
function red_compartir_user_build_content($account){  
  $edit = NULL;
  //gemini-2014
  red_compartir_user_module_invoke('view', $edit, $account);
  // Allow modules to modify the fully-built profile.
  drupal_alter('profile', $account);

  return $account->content;
}
function red_compartir_user_module_invoke($type, &$array, &$user, $category = NULL) {
  foreach (module_list() as $module) {
    $function = $module .'_user';
    //gemini-2014
    if($function=='profile_user'){
        red_compartir_profile_user($type, $array, $user, $category);
    }else{
        if (function_exists($function)) {
          $function($type, $array, $user, $category);
        }
    }    
  }
}
function red_compartir_profile_user($type, &$edit, &$user, $category = NULL) {
  switch ($type) {
    case 'load':
      return profile_load_profile($user);
    case 'register':
      return profile_form_profile($edit, $user, $category, TRUE);
    case 'update':
    return profile_save_profile($edit, $user, $category);
    case 'insert':
      return profile_save_profile($edit, $user, $category, TRUE);
    case 'view':
      //gemini-2014  
      //return profile_view_profile($user);
      return red_compartir_profile_view_profile($user);  
    case 'form':
      return profile_form_profile($edit, $user, $category);
    case 'validate':
      return profile_validate_profile($edit, $category);
    case 'categories':
      return profile_categories();
    case 'delete':
      db_query('DELETE FROM {profile_values} WHERE uid = %d', $user->uid);
  }
}
function red_compartir_profile_view_profile(&$user) {

  profile_load_profile($user);

  // Show private fields to administrators and people viewing their own account.
  //gemini-2014
  //if (user_access('administer users') || $GLOBALS['user']->uid == $user->uid) {
    $result = db_query('SELECT * FROM {profile_fields} WHERE visibility != %d ORDER BY category, weight', PROFILE_HIDDEN);
  /*}
  else {
    $result = db_query('SELECT * FROM {profile_fields} WHERE visibility != %d AND visibility != %d ORDER BY category, weight', PROFILE_PRIVATE, PROFILE_HIDDEN);
  }*/

  $fields = array();
  while ($field = db_fetch_object($result)) {
    if ($value = profile_view_field($user, $field)) {
      $title = ($field->type != 'checkbox') ? check_plain($field->title) : NULL;

      // Create a single fieldset for each category.
      if (!isset($user->content[$field->category])) {
        $user->content[$field->category] = array(
          '#type' => 'user_profile_category',
          '#title' => $field->category,
        );
      }

      $user->content[$field->category][$field->name] = array(
        '#type' => 'user_profile_item',
        '#title' => $title,
        '#value' => $value,
        '#weight' => $field->weight,
        '#attributes' => array('class' => 'profile-'. $field->name),
      );
    }
  }
}