<?php

/**
 * Returns the node type additional fieldset for Mailchimp Campaign.
 * @ingroup forms
 */
function mailchimp_campaign_get_node_type_form($form = array()) {
  $node_type = $form['#node_type']->type;
  $form['mailchimp_campaign'] = array(
    '#type' => 'fieldset',
    '#title' => t('Mailchimp Campaign'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['mailchimp_campaign']['mailchimp_campaign_is_campaign_type'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use this content type as a Mailchimp Campaign'),
    '#default_value' => variable_get('mailchimp_campaign_type_' . $node_type, FALSE),
  );
  $form = mailchimp_campaign_get_options_form($form, $node_type);
  return $form;
}

/**
 * Returns the mailchimp API options form.
 * @ingroup forms
 */
function mailchimp_campaign_get_options_form($form = array(), $node_type = '', $node = FALSE) {
  // Get the stored array of Mailchimp Campaign options.
  $options = unserialize(variable_get('mailchimp_campaign_options_' . $node_type, ''));

  if ($node) { // Varients to use on the node form
    // Check to see whether this form has stored database values.
    $result = db_query('SELECT options FROM {mailchimp_campaign} WHERE nid = %d', $node->nid);
    if ($saved_options = db_fetch_array($result)) {
      $options = unserialize($saved_options['options']);
    }

    $form_state = $form;
    $form = array();
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Prepare this campaign'),
    );
    $form['save'] = array(
      '#type' => 'submit',
      '#value' => t('Save for later'),
    );
    $form['#submit'][] = 'mailchimp_campaign_prepare_submit';
    $form['mailchimp_campaign']['options'] = array(
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#title' => t('Custom options for this campaign'),
      '#description' => t('Default values are set on the !type. Enter or tweak specific values for this particular campaign on this form.', 
        array('!type' => l('content type edit form', 'admin/content/node-type/' . $node_type))),
    );
    $form['nid'] = array(
      '#type' => 'hidden',
      '#value' => $node->nid,
    );
  }
  else {
    $form['mailchimp_campaign']['options'] = array(
      '#type' => 'fieldset',
      '#title' => t('Default options'),
      '#description' => t('Enter the values you wish to set as defaults for this content type. See %docs', array('%docs' => 'http://apidocs.mailchimp.com/1.3/campaigncreate.func.php')),
    );
  }
  $form['mailchimp_campaign']['options']['mailchimp_campaign_list_id'] = array(
    '#type' => 'textfield',
    '#title' => t('list_id'),
    '#default_value' => $options['mailchimp_campaign_list_id'],
    '#description' => t('(string) the list to send this campaign to'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_subject'] = array(
    '#type' => 'textfield',
    '#title' => t('subject'),
    '#default_value' => $options['mailchimp_campaign_subject'],
    '#description' => t('(string) the subject line for your campaign message. Leave empty to use the node title.'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_from_email'] = array(
    '#type' => 'textfield',
    '#title' => t('from_email'),
    '#default_value' => $options['mailchimp_campaign_from_email'],
    '#description' => t('(string) the From: email address for your campaign message'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_from_name'] = array(
    '#type' => 'textfield',
    '#title' => t('from_name'),
    '#default_value' => $options['mailchimp_campaign_from_name'],
    '#description' => t('(string) from_name the From: name for your campaign message (not an email address)'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_to_name'] = array(
    '#type' => 'textfield',
    '#title' => t('to_name'),
    '#default_value' => $options['mailchimp_campaign_to_name'],
    '#description' => t('the To: name recipients will see (not email address)'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_template_id'] = array(
    '#type' => 'textfield',
    '#title' => t('template_id'),
    '#default_value' => $options['mailchimp_campaign_template_id'],
    '#description' => t('(int) optional - use this user-created template to generate the HTML content of the campaign (takes precendence over other template options)'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_gallery_template_id'] = array(
    '#type' => 'textfield',
    '#title' => t('gallery_template_id'),
    '#default_value' => $options['mailchimp_campaign_gallery_template_id'],
    '#description' => t('(int) optional - use a template from the public gallery to generate the HTML content of the campaign (takes precendence over base template options)'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_base_template_id'] = array(
    '#type' => 'textfield',
    '#title' => t('base_template_id'),
    '#default_value' => $options['mailchimp_campaign_base_template_id'],
    '#description' => t('(int) optional - use this a base/start-from-scratch template to generate the HTML content of the campaign'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_html_id'] = array(
    '#type' => 'textfield',
    '#title' => t('html_id'),
    '#default_value' => $options['mailchimp_campaign_html_id'],
    '#description' => t('If using a template where will you be placing the node content?
      To find this you can look through your template for mc:edit="std_content00" sections for the body placement.
      The part in the double quotes is what you want to paste here. e.g.: html_std_content00'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_folder_id'] = array(
    '#type' => 'textfield',
    '#title' => t('folder_id'),
    '#default_value' => $options['mailchimp_campaign_folder_id'],
    '#description' => t('(int) optional - automatically file the new campaign in the folder_id passed. Get using folders() - note that Campaigns and Autoresponders have separate folder setups'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_tracking'] = array(
    '#type' => 'checkboxes',
    '#title' => t('tracking'),
    '#default_value' => $options['mailchimp_campaign_tracking'] ? $options['mailchimp_campaign_tracking'] : array(),
    '#options' => array(
      'opens' => t('Opens'),
      'html_clicks' => t('HTML clicks'),
      'text_clicks' => t('Text clicks'),
    ),
    '#description' => t('(int) optional - set which recipient actions will be tracked, as a struct of boolean values with the following keys: "opens", "html_clicks", and "text_clicks". By default, opens and HTML clicks will be tracked. Click tracking can not be disabled for Free accounts.'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_title'] = array(
    '#type' => 'textfield',
    '#title' => t('title'),
    '#default_value' => $options['mailchimp_campaign_title'],
    '#description' => t('(string) optional - an internal name to use for this campaign. By default, the campaign subject will be used.'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_authenticate'] = array(
    '#type' => 'checkbox',
    '#title' => t('authenticate'),
    '#default_value' => $options['mailchimp_campaign_authenticate'],
    '#description' => t('optional - set to true to enable SenderID, DomainKeys, and DKIM authentication, defaults to false.'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_analytics'] = array(
    '#type' => 'textfield',
    '#title' => t('analytics'),
    '#default_value' => $options['mailchimp_campaign_analytics'],
    '#description' => t('optional - Enter an id for this campaign for tracking in your analytics account.'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_auto_footer'] = array(
    '#type' => 'checkbox',
    '#title' => t('auto_footer'),
    '#default_value' => $options['mailchimp_campaign_auto_footer'],
    '#description' => t('optional - Whether or not we should auto-generate the footer for your content. Mostly useful for content from URLs or Imports'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_inline_css'] = array(
    '#type' => 'checkbox',
    '#title' => t('inline_css'),
    '#default_value' => $options['mailchimp_campaign_inline_css'],
    '#description' => t('optional - Whether or not css should be automatically inlined when this campaign is sent, defaults to false.'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_generate_text'] = array(
    '#type' => 'checkbox',
    '#title' => t('generate_text'),
    '#default_value' => $options['mailchimp_campaign_generate_text'],
    '#description' => t('optional - Whether of not to auto-generate your Text content from the HTML content. Note that this will be ignored if the Text part of the content passed is not empty, defaults to false.'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_auto_tweet'] = array(
    '#type' => 'checkbox',
    '#title' => t('auto_tweet'),
    '#default_value' => $options['mailchimp_campaign_auto_tweet'],
    '#description' => t('optional - If set, this campaign will be auto-tweeted when it is sent - defaults to false. Note that if a Twitter account isn\'t linked, this will be silently ignored.'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_auto_fb_post'] = array(
    '#type' => 'textfield',
    '#title' => t('auto_fb_post'),
    '#default_value' => $options['mailchimp_campaign_auto_fb_post'],
    '#description' => t('optional - If set, this campaign will be auto-posted to the page_ids contained in the array. If a Facebook account isn\'t linked or the account does not have permission to post to the page_ids requested, those failures will be silently ignored.'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_timewarp'] = array(
    '#type' => 'checkbox',
    '#title' => t('timewarp'),
    '#default_value' => $options['mailchimp_campaign_timewarp'],
    '#description' => t('optional - If set, this campaign must be scheduled 24 hours in advance of sending - default to false. Only valid for "regular" campaigns and "absplit" campaigns that split on schedule_time.'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_ecomm360'] = array(
    '#type' => 'checkbox',
    '#title' => t('ecomm360'),
    '#default_value' => $options['mailchimp_campaign_ecomm360'],
    '#description' => t('optional - If set, our Ecommerce360 tracking will be enabled for links in the campaign.'),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_segment'] = array(
    '#type' => 'textarea',
    '#title' => t('Segmentation options'),
    '#default_value' => $options['mailchimp_campaign_segment'],
    '#description' => t('If you want to send to a segment of your list you may enter segment conditions in this textarea. Enter one condition per row and split the condition into three parts using a pipe (|) character. The first part is the field, second is the operator and third is the value. Example: email|like|mailchimp. Note that this is a complex feature and takes a bit of work to understand. Start by reading this: !apidoc', array('!apidoc' => l('API docs', 'http://apidocs.mailchimp.com/rtfm/campaignsegmenttest.func.php'))),
  );
  $form['mailchimp_campaign']['options']['mailchimp_campaign_segment_match'] = array(
    '#type' => 'select',
    '#title' => t('Segmentation match operator type'),
    '#default_value' => $options['mailchimp_campaign_segment_match'],
    '#description' => t('controls whether to use AND or OR when applying your options - expects "any" (for OR) or "all" (for AND)'),
    '#options' => array(
      'any' => t('any'),
      'all' => t('and'),
    ),
  );
  if ($node) { // Varients to use on the node form
    $form['mailchimp_campaign']['options']['test'] = array(
      '#type' => 'submit',
      '#value' => t('Test segment'),
    );
  }
  return $form;
}

/**
 * Returns the send form
 * @ingroup forms
 */
function mailchimp_campaign_get_send_form($form_state, $cid) {
  global $user;
  $form['mailchimp_campaign_test'] = array(
    '#type' => 'fieldset',
    '#title' => t('Send a test'),
  );
  $form['mailchimp_campaign_test']['email'] = array(
    '#type' => 'textfield',
    '#title' => t('Email address'),
    '#default_value' => $user->mail,
    '#description' => t('Choose an email address to send this test to.'),
  );
  $form['mailchimp_campaign_test']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Send test'),
  );
  $form['send'] = array(
    '#type' => 'submit',
    '#value' => t('Send this campaign now'),
  );
  $form['delete'] = array(
    '#type' => 'submit',
    '#value' => t('Re-prepare campaign'),
  );
  $form['view'] = array(
    '#type' => 'markup',
    '#value' => l('View content', 'mailchimp/campaign/nojs/view/' . $cid, array('attributes' => array('class' => 'ctools-use-dialog'))),
  );

  $form['cid'] = array(
    '#type' => 'hidden',
    '#value' => $cid,
  );
  $form['#submit'][] = 'mailchimp_campaign_send_submit';

  return $form;
}

/**
 * Returns a read-only form listing all the Mailchimp settings for a prepared campaign
 * @ingroup forms
 */
function mailchimp_campaign_get_prepared_form($form_state, $data) {
  $form = array();
  $form['mailchimp_values'] = array(
    '#title' => t('Mailchimp campaign values'),
    '#description' => t('The following values are stored by Mailchimp for this campaign.'),
    '#type' => 'fieldset',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  foreach($data as $key => $value) {
    if (is_array($value)) {
      foreach ($value as $subkey => $subvalue) {
        $form['mailchimp_values'][$subkey] = array(
          '#title' => $subkey,
          '#value' => $subvalue,
          '#type' => 'item',
        );
      }
    }
    else {
      $form['mailchimp_values'][$key] = array(
        '#title' => $key,
        '#value' => $value,
        '#type' => 'item',
      );
    }
  }
  return $form;
}