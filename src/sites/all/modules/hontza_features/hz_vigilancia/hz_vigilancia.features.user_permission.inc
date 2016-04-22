<?php

/**
 * Implementation of hook_user_default_permissions().
 */
function hz_vigilancia_user_default_permissions() {
  $permissions = array();

  // Exported permission: create canal_busqueda content
  $permissions['create canal_busqueda content'] = array(
    'name' => 'create canal_busqueda content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: create canal_de_supercanal content
  $permissions['create canal_de_supercanal content'] = array(
    'name' => 'create canal_de_supercanal content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: create canal_de_yql content
  $permissions['create canal_de_yql content'] = array(
    'name' => 'create canal_de_yql content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: create item content
  $permissions['create item content'] = array(
    'name' => 'create item content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: create noticia content
  $permissions['create noticia content'] = array(
    'name' => 'create noticia content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'Creador de Grupos',
      '2' => 'authenticated user',
    ),
  );

  // Exported permission: delete any canal_busqueda content
  $permissions['delete any canal_busqueda content'] = array(
    'name' => 'delete any canal_busqueda content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: delete any canal_de_supercanal content
  $permissions['delete any canal_de_supercanal content'] = array(
    'name' => 'delete any canal_de_supercanal content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: delete any canal_de_yql content
  $permissions['delete any canal_de_yql content'] = array(
    'name' => 'delete any canal_de_yql content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: delete any item content
  $permissions['delete any item content'] = array(
    'name' => 'delete any item content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: delete any noticia content
  $permissions['delete any noticia content'] = array(
    'name' => 'delete any noticia content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: delete own canal_busqueda content
  $permissions['delete own canal_busqueda content'] = array(
    'name' => 'delete own canal_busqueda content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: delete own canal_de_supercanal content
  $permissions['delete own canal_de_supercanal content'] = array(
    'name' => 'delete own canal_de_supercanal content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: delete own canal_de_yql content
  $permissions['delete own canal_de_yql content'] = array(
    'name' => 'delete own canal_de_yql content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: delete own item content
  $permissions['delete own item content'] = array(
    'name' => 'delete own item content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: delete own noticia content
  $permissions['delete own noticia content'] = array(
    'name' => 'delete own noticia content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: edit any canal_busqueda content
  $permissions['edit any canal_busqueda content'] = array(
    'name' => 'edit any canal_busqueda content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit any canal_de_supercanal content
  $permissions['edit any canal_de_supercanal content'] = array(
    'name' => 'edit any canal_de_supercanal content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit any canal_de_yql content
  $permissions['edit any canal_de_yql content'] = array(
    'name' => 'edit any canal_de_yql content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit any item content
  $permissions['edit any item content'] = array(
    'name' => 'edit any item content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: edit any noticia content
  $permissions['edit any noticia content'] = array(
    'name' => 'edit any noticia content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit own canal_busqueda content
  $permissions['edit own canal_busqueda content'] = array(
    'name' => 'edit own canal_busqueda content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: edit own canal_de_supercanal content
  $permissions['edit own canal_de_supercanal content'] = array(
    'name' => 'edit own canal_de_supercanal content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: edit own canal_de_yql content
  $permissions['edit own canal_de_yql content'] = array(
    'name' => 'edit own canal_de_yql content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit own item content
  $permissions['edit own item content'] = array(
    'name' => 'edit own item content',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  // Exported permission: edit own noticia content
  $permissions['edit own noticia content'] = array(
    'name' => 'edit own noticia content',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  return $permissions;
}
