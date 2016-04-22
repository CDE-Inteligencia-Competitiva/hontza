<?php
/**
 * @file hontza.profile
 *
 */

/**
 * Implementation of hook_profile_details()
 */
function hontza_profile_details() {  
  return array(
    'name' => 'Hontza',
    'description' => st('Herramienta para la vigilacia competitiva'),
  );
} 

/**
 * Implementation of hook_profile_modules().
 */
function hontza_profile_modules() {
  $core_modules = array(
    // Required core modules
    'block', 'filter', 'node', 'system', 'user', 'php',
    // Optional core modules.
    'comment', 'contact', 'help', 
    //'locale', 
    'menu', 'path', 'profile', 'search', 'taxonomy', 'trigger', 'update', 'upload', 
  );

  $contributed_modules = array(
    //misc stand-alone, required by others
    'token', 'devel', 'flag', 'flag_actions', 'imce', 'pathauto', 'install_profile_api', 'jquery_ui', 'job_scheduler', 'diff',
    // ctools, panels
	  'ctools', 'page_manager', 
    //Other
    'comment_upload', 'faq', 'purl', 'quant',
    //taxonomy
    'community_tags', 'tagadelic',
    //user interface
    'wysiwyg', 'imce_wysiwyg', 
    //date
    'date_api', 
    //'date', 'date_timezone', 'date_popup', 'date_tools',
    //imagecache
    'imageapi', 'imageapi_gd', 'imagecache', 'imagecache_ui', 
    //cck
    'content', 'content_copy', 'fieldgroup', 'filefield', 'imagefield', 'link', 'number',
    'optionwidgets', 'text', 'nodereference', 'userreference', 'content_taxonomy', 
    'content_taxonomy_autocomplete', 'content_taxonomy_options',   
    //OG
    'og', 'og_access', 'og_views', 'og_vocab', 
    //spaces
    'spaces', 'spaces_customtext', 'spaces_og', 'spaces_taxonomy', 'spaces_ui', 'spaces_user', 'spaces_dashboard',  
    //views
    'views', 'views_export', 'views_ui', 'tagadelic_views', 'views_bulk_operations', 'views_or',
    //feeds
    'feeds', 'feeds_ui',
	  //context
	  'context','context_ui', 'context_layouts', 
    // requries ctools
    'strongarm', 
    // distribution management
    'features',
    'node_export',
    // misc modules easing development/maintenance
    //'openidadmin',
    // l10n
    //'l10n_update',
  );

  return array_merge($core_modules, $contributed_modules);
} 

/**
 * Features module and OpenPublish features
 */
function hontza_feature_modules() {
  $features = array(
    //investic custom modules
    'honza',
    'hontza_grupos',
    'hontzafeeds',
    'hontza_viewsfield',
    'hontza_notify',
    //Features
    'hz_core',  
    'hz_area_debate',  
    'hz_area_trabajo',  
    'hz_fuentes',  
    'hz_publica',  
    'hz_vigilancia',
    'hz_gestion',
    'hz_home_og',
	  // Custom modules developed for Investic.
	  //'hontza', 'hontza_grupos', 'hontzafeeds', 'hontza_viewsfield', 'hontza_notify',
  );
  return $features;
}

/**
 * Return a list of tasks that this profile supports.
 *
 * @return
 *   A keyed array of tasks the profile will perform during
 *   the final stage. The keys of the array will be used internally,
 *   while the values will be displayed to the user in the installer
 *   task list.
 */
function hontza_profile_task_list() {
  global $conf;
  $conf['site_name'] = 'Hontza vigilancia competitiva';
  $conf['site_footer'] = 'Hontza by <a href="http://investic.com">Investic corp</a>';
  
  $tasks['hz-configure-batch'] = st('Configure Hontza');
  if (_hontza_language_selected()) {
  //  $tasks['hz-translation-import-batch'] = st('Importing translations');
  }
  
  return $tasks;
}

/**
 * Implementation of hook_profile_tasks().
 */
function hontza_profile_tasks(&$task, $url) {
  global $install_locale;
  $output = "";
  
  install_include(hontza_profile_modules());

  if($task == 'profile') {
    drupal_set_title(t('Hontza Installation'));
    _hontza_log(t('Starting Installation'));
    _hontza_base_settings();
    $task = "hz-configure";
  }
    
  if($task == 'hz-configure') {
    $batch['title'] = st('Configuring @drupal', array('@drupal' => drupal_install_profile_name()));
    $files = module_rebuild_cache();
    foreach ( hontza_feature_modules() as $feature ) {   
      $batch['operations'][] = array('_install_module_batch', array($feature, $files[$feature]->info['name']));      
      //-- Initialize each feature individually rather then all together in the end, to avoid execution timeout.
      $batch['operations'][] = array('features_flush_caches', array()); 
    }    
    $batch['operations'][] = array('_hontza_set_permissions', array());      
    $batch['operations'][] = array('_hontza_initialize_settings', array());      
    $batch['operations'][] = array('_hontza_placeholder_content', array());      
    //$batch['operations'][] = array('_hontza_faq_content', array());      
    $batch['operations'][] = array('_hontza_set_views', array());      
    $batch['operations'][] = array('_hontza_install_menus', array());      
    $batch['operations'][] = array('_hontza_install_taxonomy', array());      
    $batch['operations'][] = array('_hontza_setup_blocks', array()); 
    $batch['operations'][] = array('_hontzalish_cleanup', array());      
    $batch['error_message'] = st('There was an error configuring @drupal.', array('@drupal' => drupal_install_profile_name()));
    $batch['finished'] = '_hontza_configure_finished';
    variable_set('install_task', 'hz-configure-batch');
    batch_set($batch);
    batch_process($url, $url);
  }

  if ($task == 'hz-translation-import') {
    if (_hontza_language_selected() && module_exists('l10n_update')) {
      module_load_install('l10n_update');
      module_load_include('batch.inc', 'l10n_update');

      $history = l10n_update_get_history();
      $available = l10n_update_available_releases();
      $updates = l10n_update_build_updates($history, $available);

      // Filter out updates in other languages. If no languages, all of them will be updated
      $updates = _l10n_update_prepare_updates($updates, NULL, array($install_locale));

      // Edited strings are kept, only default ones (previously imported)
      // are overwritten and new strings are added
      $mode = 1;

      if ($batch = l10n_update_batch_multiple($updates, $mode)) {
        $batch['finished'] = '_hontza_import_translations_finished';
        variable_set('install_task', 'hz-translation-import-batch');
        batch_set($batch);
        batch_process($url, $url);
      }
    }
  }
  
  // Land here until the batches are done
  if (in_array($task, array('hz-translation-import-batch', 'hz-configure-batch'))) {
    include_once 'includes/batch.inc';
    $output = _batch_page();
  }
    
  return $output;
} 

/**
 * Translation import process is finished, move on to the next step
 */
function _hontza_import_translations_finished($success, $results) {
  _hontza_log(t('Translations have been imported.'));
  /**
   * Necessary as the openpublish_theme's status gets reset to 0
   * by a part of the automated batch translation in l10n_update
   */
  install_default_theme('sky');
  variable_set('install_task', 'profile-finished');
}

/**
 * Import process is finished, move on to the next step
 */
function _hontza_configure_finished($success, $results) {
  _hontza_log(t('Hontza has been installed.'));
  if (_hontza_language_selected()) {
    // Other language, different part of the process
    variable_set('install_task', 'hz-translation-import');
  }
  else {
    // English installation
    variable_set('install_task', 'profile-finished');
  }
}

/**
 * Do some basic setup
 */
function _hontza_base_settings() {  
  global $base_url;  

  // create pictures dir
  $pictures_path = file_create_path('pictures');
  file_check_directory($pictures_path,TRUE);

  // Set distro tracker server URL for this distribution
//  distro_set_tracker_server('http://tracker.openpublishapp.com/distro/components');
  //variable_set('openpublish_version', '2.2'); 
 
  $types = array(
    array(
      'type' => 'page',
      'name' => st('Page'),
      'module' => 'node',
      'description' => st("A <em>page</em>, similar in form to a <em>story</em>, is a simple method for creating and displaying information that rarely changes, such as an \"About us\" section of a website. By default, a <em>page</em> entry does not allow visitor comments and is not featured on the site's initial home page."),
      'custom' => TRUE,
      'modified' => TRUE,
      'locked' => FALSE,
      'help' => '',
      'min_word_count' => '',
    ),   
  );

  foreach ($types as $type) {
    $type = (object) _node_type_set_defaults($type);
    node_type_save($type);
  }

  // Default page to not be promoted and have comments disabled.
  variable_set('node_options_page', array('status'));
  variable_set('comment_page', COMMENT_NODE_DISABLED);

  // Theme related.  
  install_default_theme('sky');
  install_admin_theme('sky');	
  variable_set('node_admin_theme', TRUE);    
  
  $theme_settings = variable_get('theme_settings', array());
  $theme_settings['toggle_node_info_page'] = FALSE;
  $theme_settings['site_slogan'] = FALSE;
  //$theme_settings['site_name'] = FALSE;
  $theme_settings['default_logo'] = FALSE;
  $theme_settings['logo_path'] = 'sites/default/files/logo.png';
  variable_set('theme_settings', $theme_settings);    
  
  // Basic Drupal settings.
  variable_set('site_frontpage', 'node');
  variable_set('user_register', 1); 
  variable_set('user_pictures', '1');
  //variable_set('statistics_count_content_views', 1);
  variable_set('filter_default_format', '1');
  
  // Set the default timezone name from the offset
  //$offset = variable_get('date_default_timezone', '7200');
  //$tzname = timezone_name_from_abbr("Europe/Madrid", $offset, 0);

  // In Aegir install processes, we need to init strongarm manually as a
  // separate page load isn't available to do this for us.
  /*
  if (function_exists('strongarm_init')) {
    strongarm_init();
  }
  $revert = array(
    'hz_analisis'=> array('user_permission', 'variable'),  
    'hz_area_debate'=> array('user_permission', 'variable'),  
    'hz_area_trabajo'=> array('user_permission', 'variable'),  
    'hz_fuentes'=> array('user_permission', 'variable'),  
    'hz_gestion'=> array('user_permission', 'variable'),  
    'hz_home_publica'=> array('user_permission', 'variable'),  
    'hz_mis_contenidos'=> array('user_permission', 'variable'),  
    'hz_usuarios_og'=> array('user_permission', 'variable'),  
    'hz_vigilancia'=> array('user_permission', 'variable'),
    'hz_portada_og'=> array('user_permission', 'variable'),
    'hz_core'=> array('user_permission', 'variable'),  
  );
  features_revert($revert);
*/
  variable_set('date_default_timezone_name', $tzname);
  
  _hontza_log(st('Configured basic settings'));
}


/**
 * Configure user/role/permission data
 */
function _hontza_set_permissions(&$context){
   
  //-- Disable titles for all views-driven blocks, by default, to avoid double-titling:
  // Profile Fields
  $profile_nombre = array(
    'title' => 'Nombre', 
	  'name' => 'profile_nombre',
    'category' => 'Datos personales',
    'type' => 'textfield',
  	'required'=> 1,
  	'register'=> 1,
  	'visibility' => 1,		
  	'weight' => -10,	
  );
  $profile_apellidos = array(
    'title' => 'Surname',
	  'name' => 'profile_apellidos',
    'category' => 'Datos personales',
    'type' => 'textfield',
  	'required'=> 1,
  	'register'=> 1,
  	'visibility' => 1,		
  	'weight' => -9,	
  );
  $profile_empresa = array(
    'title' => 'Company',
	  'name' => 'profile_empresa',
    'category' => 'Company',
    'type' => 'textfield',
  	'required'=> 1,
  	'register'=> 1,
  	'visibility' => 1,		
  	'weight' => -8,	
  );
  install_profile_field_add($profile_nombre);
  install_profile_field_add($profile_apellidos);
  install_profile_field_add($profile_empresa);

  
  $context['message'] = st('Configured Permissions');
}

/**
 * Set misc settings
 */
function _hontza_initialize_settings(&$context){
  
  db_query("INSERT INTO `flags` (`fid`, `content_type`, `name`, `title`, `roles`, `global`, `options`) VALUES
(2, 'node', 'leido_interesante', 'Leidas Interesantes', '2', 1, 'a:13:{s:10:\"flag_short\";s:1:\".\";s:9:\"flag_long\";s:0:\"\";s:12:\"flag_message\";s:0:\"\";s:17:\"flag_confirmation\";s:0:\"\";s:12:\"unflag_short\";s:1:\".\";s:11:\"unflag_long\";s:0:\"\";s:14:\"unflag_message\";s:0:\"\";s:19:\"unflag_confirmation\";s:0:\"\";s:9:\"link_type\";s:6:\"toggle\";s:12:\"show_on_page\";i:1;s:14:\"show_on_teaser\";i:1;s:12:\"show_on_form\";i:1;s:4:\"i18n\";i:0;}'),
(3, 'node', 'leido_no_interesante', 'Leido No interesante', '2', 1, 'a:13:{s:10:\"flag_short\";s:1:\".\";s:9:\"flag_long\";s:0:\"\";s:12:\"flag_message\";s:0:\"\";s:17:\"flag_confirmation\";s:0:\"\";s:12:\"unflag_short\";s:1:\".\";s:11:\"unflag_long\";s:0:\"\";s:14:\"unflag_message\";s:0:\"\";s:19:\"unflag_confirmation\";s:0:\"\";s:9:\"link_type\";s:6:\"toggle\";s:12:\"show_on_page\";i:1;s:14:\"show_on_teaser\";i:1;s:12:\"show_on_form\";i:1;s:4:\"i18n\";i:0;}');");
  db_query("INSERT INTO `flag_types` (`fid`, `type`) VALUE (2, 'item'), (2, 'noticia'), (3, 'item'), (3, 'noticia')");
  db_query("INSERT INTO `role` (`rid`, `name`) VALUES  (5 , 'Administrador'");
 

  $msg = st('Setup general configuration');
  _hontza_log($msg);
  $context['message'] = $msg;
}

/**
 * Create some content of type "page" as placeholders for content
 * and so menu items can be created
 */
function _hontza_placeholder_content(&$context) {
  global $base_url;  
  $user = user_load(array('uid' => 1));
  $page = array (
    'type' => 'page',
    'language' => 'en',
    'uid' => 1,
    'status' => 1,
    'comment' => 0,
    'promote' => 0,
    'moderate' => 0,
    'sticky' => 0,
    'tnid' => 0,
    'translate' => 0,    
    'revision_uid' => 1,
    'title' => st('Default'),
    'body' => 'Placeholder',    
    'format' => 2,
    'name' => $user->name,
  );
  
  $about_us = (object) $page;
  $about_us->title = st('About Us');
  node_save($about_us);	
  
  node_access_rebuild();
  menu_rebuild();
  
  $context['message'] = st('Installed Content');
    
//  install_node_export_import_from_file('/var/www/hontza1.investic.net/profiles/hontza/node-faq1.export');
  $context['message'] = st('Installed faq peque');
	$context['message'] = st('Previo a instalar la faq');
	//$contenido = install_node_export_import_from_file('/var/www/hontza1.investic.net/profiles/hontza/node-export_faq.export');
  install_node_export_import_from_file('/var/www/hontza1.investic.net/profiles/hontza/node-export_faq.export');

	$context['message'] = st('Installed faq EN SU TOTALIDAD');
}

/**
 * Create content Faq withd node_export module
 * 
 */
/*
function _hontza_faq_content(&$context) {
	global $base_url;
 // install_node_export_import_from_file('node-export_faq.export');
  $context['message'] = st('Installed faq');
}
*/


/**
 * Load views
 */
function _hontza_set_views() {
  //views_include_default_views();
  
  //popular view is disabled by default, enable it
  //$view = views_get_view('popular');
  //$view->disabled = FALSE;
  //$view->save();
} 

/**
 * Setup custom menus and primary links.
 */
function _hontza_install_menus(&$context) {
  cache_clear_all();
  menu_rebuild();

  install_menu_create_menu_item('dashboard',             'Inicio',       '', 'primary-links', 0, 1);
  $menuprimario = install_menu_create_menu_item('fuentes/todas',    'Fuentes',       '', 'primary-links', 0, 2);
  $menuprimario1 = install_menu_create_menu_item('vigilancia',   'Vigilancia',       '', 'primary-links', 0, 3);
  $menuprimario2 = install_menu_create_menu_item('area-trabajo',   'Area de trabajo',     '', 'primary-links', 0, 4);
  $menuprimario3 = install_menu_create_menu_item('area-debate',   'Area de debate',     '', 'primary-links', 0, 5);
  $menuprimario4 = install_menu_create_menu_item('usuarios/todos',   'Usuarios',       '', 'primary-links', 0, 6);
  $menuprimario5 = install_menu_create_menu_item('analisis',   'Analisis',       '', 'primary-links', 0, 7);
  $menuprimario6 = install_menu_create_menu_item('contenidos-del-grupo',   'Contenidos del grupo',       '', 'primary-links', 0, 8);
  $menuprimario7 = install_menu_create_menu_item('clasificaciones',   'Classifications',       '', 'primary-links', 0, 9);
  $menuprimario8 = install_menu_create_menu_item('servicios',   'Servicios',       '', 'primary-links', 0, 10);

  $msg = st('Installed Menus');
  
  _hontza_log($msg);
  $context['message'] = $msg;
} 

/**
 * Setup custom vocav and term
 */
function _hontza_install_taxonomy(&$context){
  cache_clear_all();
  menu_rebuild();
  $fuentes = array(
   0 => array(
     'name' => 'Categoría de la fuente',
     'content_types' => array('fuentedapper'=>1, 'supercanal'=> 1),
     'properties' => array('multiple'=>1, 'multiple'=>1),
     'terms' => array(
       0 => array(
         'name' => 'Ayudas y subvenciones',
         'description' => '',
         'properties' => array(),
      ),
       1 => array(
         'name' => 'Bases de Datos Bibliográficas',
         'description' => '',
         'properties' => array(),
      ),
       2 => array(
         'name' => 'Bases de Datos de Ofertas tecnológicas',
         'description' => '',
         'properties' => array(),
      ),
       3 => array(
         'name' => 'Bases de Datos de Patentes',
         'description' => '',
         'properties' => array(),
      ),
       4 => array(
        'name' => 'Bases de Datos de Proyectos I+D ',
        'description' => '',
        'properties' => array(),
     ),
       5 => array(
        'name' => 'Bases de Datos de Tesis doctorales',
        'description' => '',
        'properties' => array(),
     ),
       6 => array(
        'name' => 'Blogs',
        'description' => '',
        'properties' => array(),
     ),
       7 => array(
        'name' => 'Buscadores especializados',
        'description' => '',
        'properties' => array(),
     ),
       8 => array(
        'name' => 'Buscadores generales',
        'description' => '',
        'properties' => array(),
     ),
       9 => array(
        'name' => 'Directorios de Organizaciones',
        'description' => '',
        'properties' => array(),
     ),
       10 => array(
        'name' => 'Estadísticas',
        'description' => '',
        'properties' => array(),
     ),
       11 => array(
        'name' => 'Estudios de Mercado',
        'description' => '',
        'properties' => array(),
     ),
       12 => array(
        'name' => 'Eventos (cursos)',
        'description' => '',
        'properties' => array(),
     ),
       13 => array(
        'name' => 'Legislación y Normativa',
        'description' => '',
        'properties' => array(),
     ),
       14 => array(
        'name' => 'Normas Industriales',
        'description' => '',
        'properties' => array(),
     ),
       15 => array(
        'name' => 'Noticias de Mercado',
        'description' => '',
        'properties' => array(),
     ),
       16 => array(
        'name' => 'Ofertas públicas',
        'description' => '',
        'properties' => array(),
     ),
       17 => array(
        'name' => 'Portales verticales',
        'description' => '',
        'properties' => array(),
     ),
       18 => array(
        'name' => 'Recopilaciones de recursos de información',
        'description' => '',
        'properties' => array(),
     )
     ),
   ),
 );

  install_taxonomy_import($fuentes);
  //tax vacia 
    $tax2 = array(
    0 => array(
      'name' => 'Taxo 2',
      'content_types' => array('page'=>1),
      'properties' => array(),
      'terms' => array(
        0 => array(
          'name' => 'Blanco',
          'description' => '',
          'properties' => array(),
       ),
      ),
    ),
  );
	install_taxonomy_import($tax2);
 
   $types = array('item' => 1, 'noticia' => 1);
  $vocab = array(
    'tags' => TRUE,
    'required' => FALSE,
  );

  $vid = install_taxonomy_add_vocabulary('Etiquetas item', $types, $vocab);
	
	$servicios = array(
   0 => array(
     'name' => 'Categoría Servicios',
     'content_types' => array(),
     'properties' => array(),
     'terms' => array(
       0 => array(
         'name' => 'Buscar en una base de datos',
         'description' => '',
         'properties' => array(),
      ),
       1 => array(
         'name' => 'Buscar nuevas fuentes de información',
         'description' => '',
         'properties' => array(),
      ),
       2 => array(
         'name' => 'Convertir una fuente HTML en RSS',
         'description' => '',
         'properties' => array(),
      ),
       3 => array(
         'name' => 'Crear un nuevo módulo',
         'description' => '',
         'properties' => array(),
      ),
       4 => array(
        'name' => 'Despliegue de las necesidades de informacion ',
        'description' => '',
        'properties' => array(),
     ),

     ),
   ),
	);
	install_taxonomy_import($servicios);
	
	  //tax vacia 
    $tax5 = array(
    0 => array(
      'name' => 'Taxo 5',
      'content_types' => array('page'=>1),
      'properties' => array(),
      'terms' => array(
        0 => array(
          'name' => 'Blanco',
          'description' => '',
          'properties' => array(),
       ),
      ),
    ),
  );
  install_taxonomy_import($tax5);
	
  $taxonomy = array(
    0 => array(
      'name' => 'Tipo de grupo',
      'content_types' => array('Grupo'=>1),
      'properties' => array(),
      'terms' => array(
        0 => array(
          'name' => 'Colaboración',
          'description' => '',
          'properties' => array(),
       ),
        1 => array(
          'name' => 'Company',
          'description' => '',
          'properties' => array(),
       ),
        2 => array(
          'name' => 'Gestión',
          'description' => '',
          'properties' => array(),
       ),
        3 => array(
          'name' => 'Usuarion',
          'description' => '',
          'properties' => array(),
       )
      ),
    ),
  );
  install_taxonomy_import($taxonomy);

  $msg = st('Vocabularios instalados');
  _hontza_log($msg);
  $context['message'] = $msg;
	$context['message'] = st('Delete other taxonomy');
	
	//Eliminar vocabularios creados
	taxonomy_del_vocabulary(2);
	taxonomy_del_vocabulary(5);
}


/**
 * Create custom blocks and set region and pages.
 */
function _hontza_setup_blocks(&$context) {  
  global $theme_key, $base_url; 
  cache_clear_all();

  // Ensures that $theme_key gets set for new block creation
  $theme_key = 'sky';

  // install the demo ad blocks  
  $ad_base = $base_url . '/sites/all/themes/sky';
  $b2 = install_create_custom_block('<li ><a href="/user">'.t('Log In / Login').'</a></li>', 'Menu usuario anonimo', FILTER_HTML_ESCAPE );

  // Get these new boxes in blocks table
  install_init_blocks();

  //-- Disable titles for all views-driven blocks, by default, to avoid double-titling:
  
  db_query("UPDATE {blocks} SET title = '%s' WHERE module = '%s' AND delta= '%s'", 
            '<none>', 'menu', 'primary-links');


  install_disable_block('user', '0', 'sky');
  install_disable_block('user', '1', 'sky');
  install_disable_block('system', '0', 'sky');
  
  $msg = st('Configured Blocks');
  _hontza_log($msg);
  $context['message'] = $msg;
}

/**
 * Helper for setting a block's title only.
 */
function _hontza_set_block_title($title, $module, $delta, $theme) {
  db_query("UPDATE {blocks} SET title = '%s' WHERE module = '%s' AND delta = '%s' AND theme= '%s'", $title, $module, $delta, $theme);
}

/**
 * Cleanup after the install
 */
function _hontza_cleanup() {
  // DO NOT call drupal_flush_all_caches(), it disables all themes
  $functions = array(
    'drupal_rebuild_theme_registry',
    'menu_rebuild',
    'install_init_blocks',
    'views_invalidate_cache',    
    'node_types_rebuild',    
  );
  
  
  foreach ($functions as $func) {
    //$start = time();
    $func();
    //$elapsed = time() - $start;
    //error_log("####  $func took $elapsed seconds ###");
  }
  db_query("UPATE {system} SET status = 0 WHERE name = 'date'");
  db_query("UPATE {system} SET status = 0 WHERE name = 'date_timezone'");
  db_query("UPATE {system} SET status = 0 WHERE name = 'l10n_update'");
  db_query("UPATE {system} SET status = 0 WHERE name = 'locale'");


  ctools_flush_caches(); 
  cache_clear_all('*', 'cache', TRUE);  
  cache_clear_all('*', 'cache_content', TRUE);
}

/**
 * Set hontza as the default install profile
 */
function system_form_install_select_profile_form_alter(&$form, $form_state) {
  foreach($form['profile'] as $key => $element) {
    $form['profile'][$key]['#value'] = 'hontza';
  }
}


/**
 * Consolidate logging.
 */
function _hontza_log($msg) {
  error_log($msg);
  drupal_set_message($msg);
}

/**
 * Checks if installation is being done in a language other than English
 */
function _hontza_language_selected() {
  global $install_locale;
  return !empty($install_locale) && ($install_locale != 'en');
}
