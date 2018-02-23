<?php

/**
 * @file
 * Administrative page callbacks for the mailchimp campaign module.
 */

/**
 * Return a form for editing or creating an individual campaign.
 *
 * @ingroup forms
 */
function mailchimp_campaign_admin_settings() {
  $form = array();
  $form['mailchimp_campaign_colors_opens'] = array(
    '#type' => 'textfield',
    '#title' => t('Chart colours: Opens'),
    '#field_prefix' => '#',
    '#default_value' => variable_get('mailchimp_campaign_colors_opens', '444444'),
    '#maxlength' => 6,
    '#description' => t('Enter a six digit hex code for the charts open colours.'),
  );
  $form['mailchimp_campaign_colors_clicks'] = array(
    '#type' => 'textfield',
    '#title' => t('Chart colours: Clicks'),
    '#field_prefix' => '#',
    '#default_value' => variable_get('mailchimp_campaign_colors_clicks', '888888'),
    '#maxlength' => 6,
    '#description' => t('Enter a six digit hex code for the charts click colours.'),
  );
  $form['mailchimp_campaign_list_restrict'] = array(
    '#type' => 'textfield',
    '#title' => t('Restrict report to a specific list'),
    '#field_prefix' => 'ID: ',
    '#default_value' => variable_get('mailchimp_campaign_list_restrict', ''),
    '#description' => t('Enter a list id to only show data from a specific list. This can be useful if you have a seperate developer list and don\'t want to show data from it.'),
  );
  return system_settings_form($form);
}

/**
 * Formats a table for Mailchimp campaign results.
 */
function mailchimp_campaign_admin_results() {
  arg(3) == 'refresh' ? $refresh = TRUE : $refresh = FALSE;
  drupal_add_css(drupal_get_path('module', 'mailchimp_campaign') . '/mailchimp_campaign.css');
  $api = _mailchimp_campaign_api();
  $output = '';

  // Get a list of recent campaigns from Mailchimp
  $from = date('Y-m-d H:i:s', strtotime('-1 month'));
  $to = date('Y-m-d H:i:s', time());
  if ($list = variable_get('mailchimp_campaign_list_restrict', '')) {
    $return = $api->campaigns(array('list_id' => $list, 'status' => 'sent', 'sendtime_start' => $from, 'sendtime_end' => $to));
  }
  else {
    $return = $api->campaigns(array('status' => 'sent', 'sendtime_start' => $from, 'sendtime_end' => $to));
  }
  $header = array(t('Date Sent'), t('Subject'), t('Emails sent'), t('Percent opened'), t('Percent clicked'));
  $rows = array();
  
  global $user;
  if (variable_get('configurable_timezones', 1) && $user->uid && strlen($user->timezone)) {
    $timezone = $user->timezone;
  }
  else {
    $timezone = variable_get('date_default_timezone', 0);
  }
  $tz = date_default_timezone_get(); // Store the current timezone
  date_default_timezone_set('Europe/London'); // Convert to GMT for Mailchimp dates. 

  foreach ($return['data'] as $result) {
    $query = db_query('SELECT nid FROM {mailchimp_campaign} WHERE cid = "%s"', $result['id']);
    $nid = db_fetch_array($query);

    if ($refresh === FALSE) {
      $stats = cache_get('campaign_results_cid_' . $result['id']);
    }
    if (!$stats) {
      $stats = $api->campaignStats($result['id']);
      cache_set('campaign_results_cid_' . $result['id'], $stats, 'cache', time() + 600);
    }
    else {
      $stats = $stats->data;  
    }
    $total = $stats['emails_sent'];
    $opens_percent = round($stats['unique_opens'] / $total * 100);
    $users_clicked_percent = round($stats['users_who_clicked'] / $total * 100);
    $opens[] = array(strtotime($result['send_time']) * 1000, $opens_percent);
    $clicks[] = array(strtotime($result['send_time']) * 1000, $users_clicked_percent);
    $rows[] = array(
      $nid['nid'] ? l(format_date(strtotime($result['send_time']) . 'GMT', 'small', '', $timezone), 'node/' . $nid['nid'] . '/mailchimp/results') :
        format_date(strtotime($result['send_time']) . 'GMT', 'small', '', $timezone),
      $nid['nid'] ? l($result['subject'], 'node/' . $nid['nid'] . '/mailchimp/results') : $result['subject'],
      $result['emails_sent'],
      $opens_percent . '%',
      $users_clicked_percent . '%',
    );
  }
  
  // Prepare and plot a chart showing open and click rate
  $data = array(
    array(
      'label' => 'Opens',
      'data' => $opens,
      'color' => '#' . variable_get('mailchimp_campaign_colors_opens', '444444'),
    ),
    array(
      'label' => 'Clicks',
      'data' => $clicks,
      'color' => '#' . variable_get('mailchimp_campaign_colors_clicks', '888888'),
    )
  );
  $options = array(
    'xaxis' => array('mode' => 'time'),
    'yaxis' => array('min' => 0, 'max' => 100),
    'series' => array('points' => array('show' => 'true'), 'lines' => array('show' => 'true')),
    'grid' => array('borderwidth' => 1),
  );

  if (count($rows)) {
    $output .= '<h2>Open and click rate as percentages</h2>';
    $output .= theme('flot_graph', array('style' => 'width:100%;height:300px'), $data, $options);
    $output .= theme('table', $header, $rows);
  }
  else {
    $output .= t('No recent campaigns were found.');
  }

  date_default_timezone_set($tz); // Reset the timezone back to what it was before.
  return $output;
}