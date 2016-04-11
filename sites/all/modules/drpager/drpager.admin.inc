<?php
// $Id: drpager.admin.inc,v 1.13 2010/10/05 06:00:07 mattyoung Exp $
/**
 * @file drpager.admin.inc
 * admin/settings/drpager form constructor
 */




function drpager_settings_form($form_state) {
  init_theme();
  $theme_registry = theme_get_registry();
  if ($form_state['post']['drpager_replace_pager'] !== '1' && !variable_get('drpager_replace_pager', FALSE) &&
    $theme_registry['pager']['function'] != 'theme_drpager' &&
    $theme_registry['pager']['function'] != 'theme_drpagera' &&
    $theme_registry['pager']['function'] != 'theme_pager') {
    drupal_set_message(t('The \'pager\' theme hook is currently overriden with \'%function\' from \'%path\'. Selecting "Override pager with drpager" below will override it.',
             array('%function' => isset($theme_registry['pager']['function']) ? $theme_registry['pager']['function'] .'()' : $theme_registry['pager']['template'] .'.tpl.php',
                   '%path' => $theme_registry['pager']['theme path'])
            ), 'warning', FALSE);
  }

  $form['drpager_replace_pager'] = array(
    '#type' => 'checkbox',
    '#title' => t('Override pager with drpager'),
    '#default_value' => variable_get('drpager_replace_pager', FALSE),
  );

  $form['drpager_adaptive_value'] = array(
    '#type' => 'textfield',
    '#title' => t('Adaptive value'),
    '#description' => t('Switch to drpager only if more than the number of pages specified here. Set to \'0\' to unconditionally use Minimax pager. Enter -1 to use full pager with page number text input box.'),
    '#default_value' => variable_get('drpager_adaptive_value', 0),
    '#size' => 3,
  );

  $form['drpager_use_short_link_label'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use short link labels'),
    '#description' => t('Default uses \'«\', \'‹\', \'›\' and \'»\' instead of \'« first\', \'‹ previous\', \'next ›\' and \'last »\''),
    '#default_value' => variable_get('drpager_use_short_link_label', FALSE),
  );

  if (module_exists('views')) {
    if ($form_state['post']['drpager_replace_views_mini_pager'] !== '1' && !variable_get('drpager_replace_views_mini_pager', FALSE) &&
        $theme_registry['views_mini_pager']['function'] != 'theme_drpager_mini' && $theme_registry['views_mini_pager']['function'] != 'theme_views_mini_pager') {
      drupal_set_message(t('The \'views_mini_pager\' theme hook is currently overriden with \'%function\' from \'%path\'. Selecting "Override views_mini_pager with drpager" below will override it.',
               array('%function' => isset($theme_registry['views_mini_pager']['function']) ? $theme_registry['views_mini_pager']['function'] .'()' : $theme_registry['views_mini_pager']['template'] .'.tpl.php',
                     '%path' => $theme_registry['views_mini_pager']['theme path'])
              ), 'warning', FALSE);
    }

    $form['drpager_replace_views_mini_pager'] = array(
      '#type' => 'checkbox',
      '#title' => t('Override views_mini_pager with drpager'),
      '#default_value' => variable_get('drpager_replace_views_mini_pager', FALSE),
    );
  }

  $form = system_settings_form($form);
  $form['#submit'][] = '_drpager_reset';
  return $form;
}


function drpager_settings_form_validate($form, $form_state) {
  $value = $form_state['values']['drpager_adaptive_value'];
  if ($value !== (string)(int)$value) {
    form_error($form['drpager_adaptive_value'], t('Adaptive value must be an integer.'));
  }
}
