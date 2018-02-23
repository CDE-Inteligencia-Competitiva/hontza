<?php

/**
 * @file
 * A sample implementation of Mail Editor hooks for Logintoboggan module.
 *
 * Since the mail_edit module is arranged around the full key (including the calling module)
 * it can only support user_ keys at this time.
 *
 * However, the Logintoboggan module (http://drupal.org/project/logintoboggan) replaces many of the
 * mail messages and sends them itself. As a result, mail_edit is unable to deal with them.
 *
 * This simple file allows logintoboggan to work. I think a more generic approach would
 * work better (not keying things to the full mailkey ('module' + '_' + 'key') but rather
 * on the key itself.
 *
 */

/**
 * Implementation of hook_mailkeys().
 */
function logintoboggan_mailkeys() {
  if (module_exists('logintoboggan')) {
    return user_mailkeys();
  }
}

/**
 * Implementation of hook_mail_edit_tokens_list().
 */
function logintoboggan_mail_edit_tokens_list($mailkey, $options = array()) {
  return user_mail_edit_tokens_list($mailkey, $options);
}

/**
 * Implementation of hook_mail_edit_tokens_values().
 */
function logintoboggan_mail_edit_tokens_value($mailkey, $mail, $options = array()) {
  $tokens = user_mail_edit_tokens_value($mailkey, $mail, $options);
  if (module_exists('logintoboggan')) {
    if (variable_get('user_register', 1) == 1) {
      $tokens['!login_url'] = logintoboggan_eml_validate_url($mail['params']['account']);
    } 
  }
  return $tokens;
}

/**
 * Implementation of hook_mail_edit_text().
 */
function logintoboggan_mail_edit_text($mailkey, $langcode) {
  return user_mail_edit_text($mailkey, $langcode);
}