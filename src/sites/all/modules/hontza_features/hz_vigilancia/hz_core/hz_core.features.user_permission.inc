<?php

/**
 * Implementation of hook_user_default_permissions().
 */
function hz_core_user_default_permissions() {
  $permissions = array();

  //gemini-2014
  /*
  // Exported permission: Gestion fuentes publicas
  $permissions['Gestion fuentes publicas'] = array(
    'name' => 'Gestion fuentes publicas',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );*/
  
   $permissions['Public sources management'] = array(
    'name' => 'Public sources management',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  
  
  
  //gemini-2014
  /*
  // Exported permission: Ver estadisticas canales
  $permissions['Ver estadisticas canales'] = array(
    'name' => 'Ver estadisticas canales',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'Creador de Grupos',
      '2' => 'authenticated user',
    ),
  );*/
  
  // Exported permission: View channel statistics
  $permissions['View channel statistics'] = array(
    'name' => 'View channel statistics',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'Creador de Grupos',
      '2' => 'authenticated user',
    ),
  );
  
  //gemini-2014
  /*
  // Exported permission: Ver estadisticas etiquetas
  $permissions['Ver estadisticas etiquetas'] = array(
    'name' => 'Ver estadisticas etiquetas',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'Creador de Grupos',
      '2' => 'authenticated user',
    ),
  );*/
  
  // Exported permission: View tag statistics
  $permissions['View tag statistics'] = array(
    'name' => 'View tag statistics',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'Creador de Grupos',
      '2' => 'authenticated user',
    ),
  );

  // Exported permission: access all views
  $permissions['access all views'] = array(
    'name' => 'access all views',
    'roles' => array(),
  );

  // Exported permission: access comments
  $permissions['access comments'] = array(
    'name' => 'access comments',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  // Exported permission: access content
  $permissions['access content'] = array(
    'name' => 'access content',
    'roles' => array(
      '0' => 'anonymous user',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: access site-wide contact form
  $permissions['access site-wide contact form'] = array(
    'name' => 'access site-wide contact form',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  // Exported permission: access user profiles
  $permissions['access user profiles'] = array(
    'name' => 'access user profiles',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: add own group vocabulary
  $permissions['add own group vocabulary'] = array(
    'name' => 'add own group vocabulary',
    'roles' => array(),
  );

  // Exported permission: administer blocks
  $permissions['administer blocks'] = array(
    'name' => 'administer blocks',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: administer comments
  $permissions['administer comments'] = array(
    'name' => 'administer comments',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: administer faq
  $permissions['administer faq'] = array(
    'name' => 'administer faq',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: administer faq order
  $permissions['administer faq order'] = array(
    'name' => 'administer faq order',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: administer nodes
  $permissions['administer nodes'] = array(
    'name' => 'administer nodes',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: administer own group vocabulary
  $permissions['administer own group vocabulary'] = array(
    'name' => 'administer own group vocabulary',
    'roles' => array(),
  );

  // Exported permission: administer page manager
  $permissions['administer page manager'] = array(
    'name' => 'administer page manager',
    'roles' => array(),
  );

  // Exported permission: administer pathauto
  $permissions['administer pathauto'] = array(
    'name' => 'administer pathauto',
    'roles' => array(),
  );

  // Exported permission: administer permissions
  $permissions['administer permissions'] = array(
    'name' => 'administer permissions',
    'roles' => array(),
  );

  // Exported permission: administer taxonomy
  $permissions['administer taxonomy'] = array(
    'name' => 'administer taxonomy',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: administer url aliases
  $permissions['administer url aliases'] = array(
    'name' => 'administer url aliases',
    'roles' => array(),
  );

  // Exported permission: administer users
  $permissions['administer users'] = array(
    'name' => 'administer users',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: administer views
  $permissions['administer views'] = array(
    'name' => 'administer views',
    'roles' => array(),
  );

  // Exported permission: administer voting api
  $permissions['administer voting api'] = array(
    'name' => 'administer voting api',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: change own username
  $permissions['change own username'] = array(
    'name' => 'change own username',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: configure own user space
  $permissions['configure own user space'] = array(
    'name' => 'configure own user space',
    'roles' => array(),
  );

  // Exported permission: create faq
  $permissions['create faq'] = array(
    'name' => 'create faq',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: create grupo content
  $permissions['create grupo content'] = array(
    'name' => 'create grupo content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'Creador de Grupos',
    ),
  );

  // Exported permission: create page content
  $permissions['create page content'] = array(
    'name' => 'create page content',
    'roles' => array(),
  );

  // Exported permission: create servicio content
  $permissions['create servicio content'] = array(
    'name' => 'create servicio content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: create url aliases
  $permissions['create url aliases'] = array(
    'name' => 'create url aliases',
    'roles' => array(),
  );

  // Exported permission: delete any grupo content
  $permissions['delete any grupo content'] = array(
    'name' => 'delete any grupo content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: delete any page content
  $permissions['delete any page content'] = array(
    'name' => 'delete any page content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: delete any servicio content
  $permissions['delete any servicio content'] = array(
    'name' => 'delete any servicio content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: delete own grupo content
  $permissions['delete own grupo content'] = array(
    'name' => 'delete own grupo content',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'Creador de Grupos',
    ),
  );

  // Exported permission: delete own page content
  $permissions['delete own page content'] = array(
    'name' => 'delete own page content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: delete own servicio content
  $permissions['delete own servicio content'] = array(
    'name' => 'delete own servicio content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit any grupo content
  $permissions['edit any grupo content'] = array(
    'name' => 'edit any grupo content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit any page content
  $permissions['edit any page content'] = array(
    'name' => 'edit any page content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit any servicio content
  $permissions['edit any servicio content'] = array(
    'name' => 'edit any servicio content',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit faq
  $permissions['edit faq'] = array(
    'name' => 'edit faq',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit own faq
  $permissions['edit own faq'] = array(
    'name' => 'edit own faq',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  // Exported permission: edit own group term
  $permissions['edit own group term'] = array(
    'name' => 'edit own group term',
    'roles' => array(),
  );

  // Exported permission: edit own group vocabulary
  $permissions['edit own group vocabulary'] = array(
    'name' => 'edit own group vocabulary',
    'roles' => array(),
  );

  // Exported permission: edit own grupo content
  $permissions['edit own grupo content'] = array(
    'name' => 'edit own grupo content',
    'roles' => array(
      '0' => 'Creador de Grupos',
    ),
  );

  // Exported permission: edit own page content
  $permissions['edit own page content'] = array(
    'name' => 'edit own page content',
    'roles' => array(),
  );

  // Exported permission: edit own servicio content
  $permissions['edit own servicio content'] = array(
    'name' => 'edit own servicio content',
    'roles' => array(),
  );

  // Exported permission: edit own tags
  $permissions['edit own tags'] = array(
    'name' => 'edit own tags',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  // Exported permission: manage stored views
  $permissions['manage stored views'] = array(
    'name' => 'manage stored views',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: post comments
  $permissions['post comments'] = array(
    'name' => 'post comments',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  // Exported permission: post comments without approval
  $permissions['post comments without approval'] = array(
    'name' => 'post comments without approval',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  // Exported permission: rate content
  $permissions['rate content'] = array(
    'name' => 'rate content',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  //gemini-2014
  /*
  // Exported permission: selector de grupos
  $permissions['selector de grupos'] = array(
    'name' => 'selector de grupos',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );*/
  
  
  
   // Exported permission: selector de grupos
  $permissions['Group selector'] = array(
    'name' => 'Group selector',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  //gemini-2014
  /*
  // Exported permission: suscribirse a canal
  $permissions['suscribirse a canal'] = array(
    'name' => 'suscribirse a canal',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );*/

   // Exported permission: suscribirse a canal
  $permissions['channel subscription'] = array(
    'name' => 'channel subscription',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );
  
  
  
  // Exported permission: tag content
  $permissions['tag content'] = array(
    'name' => 'tag content',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  // Exported permission: upload files
  $permissions['upload files'] = array(
    'name' => 'upload files',
    'roles' => array(
      '0' => 'Administrador',
      '1' => 'authenticated user',
    ),
  );

  // Exported permission: upload files to comments
  $permissions['upload files to comments'] = array(
    'name' => 'upload files to comments',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  // Exported permission: use PHP for fivestar target
  $permissions['use PHP for fivestar target'] = array(
    'name' => 'use PHP for fivestar target',
    'roles' => array(),
  );

  // Exported permission: use page manager
  $permissions['use page manager'] = array(
    'name' => 'use page manager',
    'roles' => array(),
  );

  // Exported permission: use views exporter
  $permissions['use views exporter'] = array(
    'name' => 'use views exporter',
    'roles' => array(),
  );

  // Exported permission: view faq page
  $permissions['view faq page'] = array(
    'name' => 'view faq page',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  // Exported permission: view files uploaded to comments
  $permissions['view files uploaded to comments'] = array(
    'name' => 'view files uploaded to comments',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  // Exported permission: view uploaded files
  $permissions['view uploaded files'] = array(
    'name' => 'view uploaded files',
    'roles' => array(
      '0' => 'authenticated user',
    ),
  );

  // Exported permission: view users outside groups
  $permissions['view users outside groups'] = array(
    'name' => 'view users outside groups',
    'roles' => array(
      '0' => 'Administrador',
    ),
  );

  return $permissions;
}
