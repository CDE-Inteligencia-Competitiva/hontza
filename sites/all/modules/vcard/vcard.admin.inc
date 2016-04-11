<?php
// $Id: vcard.admin.inc,v 1.1.2.5 2011/01/19 09:11:35 sanduhrs Exp $

/**
 * @file
 * vCard - Administration UI
 */

/**
 * Callback for administration page.
 */
function vcard_admin_settings() {
  if (!_vcard_init()) {
    drupal_set_message(t('The PEAR package File_IMC or Contact_Vcard_Build (required by vcard.module) has not been installed properly, please read INSTALL.txt.'), 'error');
  }

  $form['display'] = array(
    '#type' => 'fieldset',
    '#title' => t('Display Settings'),
  );
  $form['display']['vcard_display_profile_link'] = array(
    '#type' => 'checkbox',
    '#title' => t("Show vCard download link on user's profile"),
    '#default_value' => variable_get('vcard_display_profile_link', 1),
  );
  $form['display']['vcard_display_profile_hcard'] = array(
    '#type' => 'checkbox',
    '#title' => t("Show hCard on user's profile"),
    '#default_value' => variable_get('vcard_display_profile_hcard', 1),
  );

  $options = array('' => t('<Select a property>'));
  $options = $options + _vcard_properties();
  $profile_fields = _vcard_profile_fields();
  $form['field_mappings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Field Mappings'),
    '#description' => !count($profile_fields) ? t('You must first define a few profile fields before you can map them to vCard properties. To do this, go to the !link administration section.', array('!link' => l(t('Profiles'), 'admin/config/people/profile'))) : '',
  );
  foreach ($profile_fields as $fid => $title) {
    $form['field_mappings']['vcard_profile_' . $fid] = array(
      '#type' => 'select',
      '#title' => t('Property for %title', array('%title' => $title)),
      '#default_value' => variable_get('vcard_profile_' . $fid, ''),
      '#options' => $options,
    );
  }
  $form['auto_create'] = array(
    '#type' => 'submit',
    '#value' => t('Auto-create profile fields'),
    '#description' => t("Attempt to create the normal required profile fields, if you haven't already done so'"),
  );
  $form['#submit'][] = 'vcard_auto_create_profile_submit';

  return system_settings_form($form);
}

/**
 * Add an action to auto-create a detailed contact profile with consistent
 * names.
 *
 * Capture the vcard settings submission, and act on the button if pressed.
 *
 * Although I could have just over-written everything, instead I take care to
 * absorb half-profiles that have already been made, possibly with different
 * names, and not replace anything that already works OK. This process just adds
 * the supplemental contact vCard fields. Thus it should be safe to run as an
 * upgrade over anything you already have.
 */
function vcard_auto_create_profile_submit($form, &$form_state) {
  if ($form_state['clicked_button']['#value'] == t('Auto-create profile fields')) {
    drupal_set_message(t("Creating required profile fields"));
    $required_fields = _vcard_properties();

    // Load in all currently existing fields, to see what exists and what they are matched to so far.
    $available_fields = array();
    $available_fields_result = db_query("SELECT * FROM {profile_fields} ORDER BY weight");
    while ($field = db_fetch_object($available_fields_result)) {
      // Index these on a version of the internal id, not the text label
      $key = preg_replace('|^profile_|', '', $field->name);
      if ($linked = variable_get('vcard_profile_'. $field->fid, '')) {
        // This field is already linked to a vcard tag. Add it to the index in the right place
        $key = $linked;
      }
      // There may be confusion if there are more than one field named the same. Don't do that.
      if (empty($available_fields[$key])) {
        $available_fields[$key] = $field;
      }
    }
    $vcard_fields = _vcard_profile_fields();

    foreach ($required_fields as $required_field_id => $required_field_label) {
      // Check if a field with its label is already existing
      if (in_array($required_field_id, array_keys($available_fields))) {
        $field_def = $available_fields[$required_field_id];
        drupal_set_message(t(
          "<a href='!field_link'>%field_label</a> already exists as a profile field. %alt_name",
          array(
            '%field_label' => $required_field_label,
            '!field_link'  => url('admin/user/profile/edit/' . $field_def->fid),
            '%alt_name'    => (($field_def->title != $required_field_label) ? " (called $field_def->title)" : "")
          )
        ));
        variable_set('vcard_profile_'. $field_def->name, $required_field_id);
        // don't let the form submission override this now...
        unset($form_state['values']['vcard_profile_' . $field_def->name]);
      }
      else {
        // need to make it
        drupal_set_message(t("Creating profile field @label", array('@label' => $required_field_label)));
        /**
         * @see profile_field_form_submit()
         */
        $field_def = array(
          'title'       => $required_field_label,
          'name'        => 'profile_' . $required_field_id,
          'explanation' => '',
          'category'    => 'Contact Information',
          'type'        => 'textfield',
          'weight'      => 0,
          'required'    => 0,
          'register'    => 1,
          'visibility'  => 2,
        );
        if ($required_field_id == 'url') {
          $field_def['type']='url';
        }
        if ($required_field_id == 'birthday') {
          $field_def['type']='date';
        }

        drupal_write_record('profile_fields', $field_def);
        // retrieve its new ID
        $result = db_query("SELECT * FROM {profile_fields} WHERE name = '%s'", $field_def['name']);
        $field_def = db_fetch_object($result);
      }
      // Now check the right profile field is matched up with the right vcard field
      if ($field_def && (variable_get('vcard_profile_'. $field_def->fid, '') != $required_field_id)) {
        drupal_set_message(t(
          "Assigning profile field %fid (<a href='!field_link'>%title</a>) to vcard key %required_field_id",
          array(
            '%fid' => $field_def->fid,
            '%title' => $field_def->title,
            '%required_field_id' => $required_field_id,
            '!field_link' => url('admin/user/profile/edit/'. $field_def->fid)
          )
        ));
        variable_set('vcard_profile_' . $field_def->name, $required_field_id);
        // don't let the form submission override this now...
        unset($form_state['values']['vcard_profile_' . $field_def->name]);
      }
    }
    drupal_set_message(t("
      Created profile fields! You may want to review the profile settings
      individually to ensure their display order and public visibility
      settings are right for you.
      By default they are all displayed on the users profile page.
    "));
  }
  cache_clear_all();
  menu_rebuild();
}
