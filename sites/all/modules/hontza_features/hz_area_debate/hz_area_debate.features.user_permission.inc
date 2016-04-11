<?php

/**
 * Implementation of hook_user_default_permissions().
 */
function hz_area_debate_user_default_permissions() {
  $permissions = array();

  // Exported permission: create debate content
  $permissions['create debate content'] = array(
    'name' => 'create debate content',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  // Exported permission: delete any debate content
  $permissions['delete any debate content'] = array(
    'name' => 'delete any debate content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: delete own debate content
  $permissions['delete own debate content'] = array(
    'name' => 'delete own debate content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: edit any debate content
  $permissions['edit any debate content'] = array(
    'name' => 'edit any debate content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit own debate content
  $permissions['edit own debate content'] = array(
    'name' => 'edit own debate content',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  return $permissions;
}
