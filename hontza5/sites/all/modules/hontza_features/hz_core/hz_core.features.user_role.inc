<?php

/**
 * Implementation of hook_user_default_roles().
 */
function hz_core_user_default_roles() {
  $roles = array();

  // Exported role: Administrador
  $roles['Administrador'] = array(
    'name' => 'Administrador',
  );

  // Exported role: Creador de Grupos
  $roles['Creador de Grupos'] = array(
    'name' => 'Creador de Grupos',
  );

  // Exported role: anonymous user
  $roles['anonymous user'] = array(
    'name' => 'anonymous user',
  );

  // Exported role: authenticated user
  $roles['authenticated user'] = array(
    'name' => 'authenticated user',
  );

  return $roles;
}
