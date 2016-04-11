<?php

/**
 * Implementation of hook_user_default_permissions().
 */
function hz_publica_user_default_permissions() {
  $permissions = array();

  // Exported permission: create noticias_portada content
  $permissions['create noticias_portada content'] = array(
    'name' => 'create noticias_portada content',
    'roles' => array(),
  );

  // Exported permission: create rss_feed content
  $permissions['create rss_feed content'] = array(
    'name' => 'create rss_feed content',
    'roles' => array(),
  );

  // Exported permission: create rss_feed_importador content
  $permissions['create rss_feed_importador content'] = array(
    'name' => 'create rss_feed_importador content',
    'roles' => array(),
  );

  // Exported permission: delete any noticias_portada content
  $permissions['delete any noticias_portada content'] = array(
    'name' => 'delete any noticias_portada content',
    'roles' => array(),
  );

  // Exported permission: delete any rss_feed content
  $permissions['delete any rss_feed content'] = array(
    'name' => 'delete any rss_feed content',
    'roles' => array(),
  );

  // Exported permission: delete any rss_feed_importador content
  $permissions['delete any rss_feed_importador content'] = array(
    'name' => 'delete any rss_feed_importador content',
    'roles' => array(),
  );

  // Exported permission: delete own noticias_portada content
  $permissions['delete own noticias_portada content'] = array(
    'name' => 'delete own noticias_portada content',
    'roles' => array(),
  );

  // Exported permission: delete own rss_feed content
  $permissions['delete own rss_feed content'] = array(
    'name' => 'delete own rss_feed content',
    'roles' => array(),
  );

  // Exported permission: delete own rss_feed_importador content
  $permissions['delete own rss_feed_importador content'] = array(
    'name' => 'delete own rss_feed_importador content',
    'roles' => array(),
  );

  // Exported permission: edit any noticias_portada content
  $permissions['edit any noticias_portada content'] = array(
    'name' => 'edit any noticias_portada content',
    'roles' => array(),
  );

  // Exported permission: edit any rss_feed content
  $permissions['edit any rss_feed content'] = array(
    'name' => 'edit any rss_feed content',
    'roles' => array(),
  );

  // Exported permission: edit any rss_feed_importador content
  $permissions['edit any rss_feed_importador content'] = array(
    'name' => 'edit any rss_feed_importador content',
    'roles' => array(),
  );

  // Exported permission: edit own noticias_portada content
  $permissions['edit own noticias_portada content'] = array(
    'name' => 'edit own noticias_portada content',
    'roles' => array(),
  );

  // Exported permission: edit own rss_feed content
  $permissions['edit own rss_feed content'] = array(
    'name' => 'edit own rss_feed content',
    'roles' => array(),
  );

  // Exported permission: edit own rss_feed_importador content
  $permissions['edit own rss_feed_importador content'] = array(
    'name' => 'edit own rss_feed_importador content',
    'roles' => array(),
  );

  // Exported permission: import rss_feed_aveq feeds
  $permissions['import rss_feed_aveq feeds'] = array(
    'name' => 'import rss_feed_aveq feeds',
    'roles' => array(),
  );

  return $permissions;
}
