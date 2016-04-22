<?php

/**
 * Implementation of hook_user_default_permissions().
 */
function hz_fuentes_user_default_permissions() {
  $permissions = array();

  // Exported permission: create fuentedapper content
  $permissions['create fuentedapper content'] = array(
    'name' => 'create fuentedapper content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: create supercanal content
  $permissions['create supercanal content'] = array(
    'name' => 'create supercanal content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: delete any fuentedapper content
  $permissions['delete any fuentedapper content'] = array(
    'name' => 'delete any fuentedapper content',
    'roles' => array(),
  );

  // Exported permission: delete any supercanal content
  $permissions['delete any supercanal content'] = array(
    'name' => 'delete any supercanal content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: delete own fuentedapper content
  $permissions['delete own fuentedapper content'] = array(
    'name' => 'delete own fuentedapper content',
    'roles' => array(),
  );

  // Exported permission: delete own supercanal content
  $permissions['delete own supercanal content'] = array(
    'name' => 'delete own supercanal content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: edit any fuentedapper content
  $permissions['edit any fuentedapper content'] = array(
    'name' => 'edit any fuentedapper content',
    'roles' => array(),
  );

  // Exported permission: edit any supercanal content
  $permissions['edit any supercanal content'] = array(
    'name' => 'edit any supercanal content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit own fuentedapper content
  $permissions['edit own fuentedapper content'] = array(
    'name' => 'edit own fuentedapper content',
    'roles' => array(),
  );

  // Exported permission: edit own supercanal content
  $permissions['edit own supercanal content'] = array(
    'name' => 'edit own supercanal content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  return $permissions;
}
