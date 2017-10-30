<?php
// $Id: default.settings.php,v 1.8.2.4 2009/09/14 12:59:18 goba Exp $

/**
 * @file
 * Drupal site-specific configuration file.
 *
 * IMPORTANT NOTE:
 * This file may have been set to read-only by the Drupal installation
 * program. If you make changes to this file, be sure to protect it again
 * after making your modifications. Failure to remove write permissions
 * to this file is a security risk.
 *
 * The configuration file to be loaded is based upon the rules below.
 *
 * The configuration directory will be discovered by stripping the
 * website's hostname from left to right and pathname from right to
 * left. The first configuration file found will be used and any
 * others will be ignored. If no other configuration file is found
 * then the default configuration file at 'sites/default' will be used.
 *
 * For example, for a fictitious site installed at
 * http://www.drupal.org/mysite/test/, the 'settings.php'
 * is searched in the following directories:
 *
 *  1. sites/www.drupal.org.mysite.test
 *  2. sites/drupal.org.mysite.test
 *  3. sites/org.mysite.test
 *
 *  4. sites/www.drupal.org.mysite
 *  5. sites/drupal.org.mysite
 *  6. sites/org.mysite
 *
 *  7. sites/www.drupal.org
 *  8. sites/drupal.org
 *  9. sites/org
 *
 * 10. sites/default
 *
 * If you are installing on a non-standard port number, prefix the
 * hostname with that number. For example,
 * http://www.drupal.org:8080/mysite/test/ could be loaded from
 * sites/8080.www.drupal.org.mysite.test/.
 */

/**
 * Database settings:
 *
 * Note that the $db_url variable gets parsed using PHP's built-in
 * URL parser (i.e. using the "parse_url()" function) so make sure
 * not to confuse the parser. If your username, password
 * or database name contain characters used to delineate
 * $db_url parts, you can escape them via URI hex encodings:
 *
 *   : = %3a   / = %2f   @ = %40
 *   + = %2b   ( = %28   ) = %29
 *   ? = %3f   = = %3d   & = %26
 *
 * To specify multiple connections to be used in your site (i.e. for
 * complex custom modules) you can also specify an associative array
 * of $db_url variables with the 'default' element used until otherwise
 * requested.
 *
 * You can optionally set prefixes for some or all database table names
 * by using the $db_prefix setting. If a prefix is specified, the table
 * name will be prepended with its value. Be sure to use valid database
 * characters only, usually alphanumeric and underscore. If no prefixes
 * are desired, leave it as an empty string ''.
 *
 * To have all database names prefixed, set $db_prefix as a string:
 *
 *   $db_prefix = 'main_';
 *
 * To provide prefixes for specific tables, set $db_prefix as an array.
 * The array's keys are the table names and the values are the prefixes.
 * The 'default' element holds the prefix for any tables not specified
 * elsewhere in the array. Example:
 *
 *   $db_prefix = array(
 *     'default'   => 'main_',
 *     'users'     => 'shared_',
 *     'sessions'  => 'shared_',
 *     'role'      => 'shared_',
 *     'authmap'   => 'shared_',
 *   );
 *
 * Database URL format:
 *   $db_url = 'mysql://username:password@localhost/databasename';
 *   $db_url = 'mysqli://username:password@localhost/databasename';
 *   $db_url = 'pgsql://username:password@localhost/databasename';
 */
//$db_url = 'mysql://kam:686295@mysql.e-cicerone.net/eciceronenet2';
//$db_url = 'mysql://root@localhost/eciceronenet3';
//$db_url = 'mysql://root@localhost/eciceronenet4';

//$db_url = 'mysql://root:68629527A@localhost/eciceronenet4';
//$db_url = 'mysql://hontzacde:hontza30@localhost/hontza30';
$db_url['default'] = 'mysql://hontza:hontzacde@localhost/hontza';
//$db_url['my_install'] = 'mysql://remotoa:68629527A@92.243.10.49/eciceronenet3';


$db_prefix = '';

define('_SAREKO_ID','ROOT');
define('_IS_NETWORK_HONTZA',1);
define('_IS_SOLR',1);
define('_IS_HOUND',1);
define('_IS_USUARIO_BASICO',1);
define('_IS_SOLR_SEND',1);
define('_IS_VISUALIZADOR',1);
define('_IS_NEW_FONT',1);
define('_IS_REGISTRAR_RED',1);
define('_RED_IS_PUBLICO_EXPORTAR_RSS',1);
//define('_IS_GUARDAR_BOLETIN_REPORT_AUTOMATICO',1);
define('_IS_GUARDAR_BOLETIN_REPORT_AUTOMATICO',0);
define('_IS_CUSTOM_CSS_HONTZA',1);
define('_IS_BOOKMARK',1);
define('_IS_BOOKMARK_ADMIN',1);
define('_IS_SOLR_SEND',1);
//define('_IS_SOLR_ACTUALIZAR_ITEMS',0);
define('_IS_CANAL_USUARIO',1);
define('_IS_SOLR_STATUS',1);
define('_IS_COMPARTIR_DOCUMENTOS',1);
define('_IS_ITEM_POST_URL_SAVE',1);
define('_POWERED_VERSION','v5.0');
define('_IS_NODE_TITLE_LEN',1);
define('_IS_CANAL_JSON',1);
define('_IS_FACILITADOR_WITH_USER',1);
define('_IS_CANAL_OPENCALAIS',1);
define('_IS_ESTRATEGIA_CONGELAR_VOTO',1);
//define('_RED_EXPORTAR_RSS_PREFIJO_SITES','default');
define('_IS_SOLR_ACTUALIZAR_NOTICIAS_USUARIO',1);
define('_IS_CANAL_DUPLICATE_NEWS',1);
define('_IS_GRUPO_TAB_ACTIVADO',0);
define('_IS_BOLETIN_REPORT_SHOW_ATTACHMENTS',1);
define('_IS_CRM_EXPORTAR_TEXTO',1);
define('_IS_CRM_EXPORTAR_TAG',1);
define('_IS_FEED_SANIZITER',1);
define('_IS_HOUND_FILTER',1);
define('_IS_CRM_LIST_TYPE',1);
define('_IS_CRM_ACTIVAR_CLIENTE',1);
//Red fields
define('_FIELD_RED_ITEM_WEB_NAME','field_igape_item_web');
define('_FIELD_RED_IDIOMAS_NAME','field_igape_idiomas');
define('_FIELD_RED_PERIODICIDAD_NAME','field_igape_periodicidad');
define('_FIELD_RED_URL_NAME','field_igape_url');
define('_FIELD_RED_COUNTRY_FUENTE_NAME','field_igape_country_fuente');
define('_FIELD_RED_FUENTE_REGION_NAME','field_fuente_region');
define('_FIELD_RED_FECHA_NOTICIA_NAME','field_igape_fecha_noticia');
define('_FIELD_RED_BOLETINES_NAME','field_igape_boletines');
define('_FIELD_RED_VISITAS_NAME','field_igape_visitas');
define('_FIELD_RED_RESUMEN_NAME','field_igape_resumen');
define('_FIELD_RED_SECTORIZACION_NAME','field_igape_sectorizacion');
define('_FIELD_RED_CNAE_NAME','field_igape_cnae');
define('_FIELD_RED_FUENTE_NOTICIA_NAME','field_igape_fuente_de_la_noticia');
define('_CONTENT_FIELD_RED_FECHA_NOTICIA_NAME','content_field_igape_fecha_noticia');
define('_FIELD_RED_FECHA_NOTICIA_VALUE','field_igape_fecha_noticia_value');
define('_FIELD_RED_COUNTRY_NAME','field_igape_country');
define('_FIELD_RED_IDIOMA_PRINCIPAL','field_igape_idioma_principal');

/**
 * Access control for update.php script
 *
 * If you are updating your Drupal installation using the update.php script
 * being not logged in as administrator, you will need to modify the access
 * check statement below. Change the FALSE to a TRUE to disable the access
 * check. After finishing the upgrade, be sure to open this file again
 * and change the TRUE back to a FALSE!
 */
$update_free_access = FALSE;

/**
 * Base URL (optional).
 *
 * If you are experiencing issues with different site domains,
 * uncomment the Base URL statement below (remove the leading hash sign)
 * and fill in the absolute URL to your Drupal installation.
 *
 * You might also want to force users to use a given domain.
 * See the .htaccess file for more information.
 *
 * Examples:
 *   $base_url = 'http://www.example.com';
 *   $base_url = 'http://www.example.com:8888';
 *   $base_url = 'http://www.example.com/drupal';
 *   $base_url = 'https://www.example.com:8888/drupal';
 *
 * It is not allowed to have a trailing slash; Drupal will add it
 * for you.
 */
 //$base_url = 'http://localhost/hontza';  // NO trailing slash!
 /**
 * PHP settings:
 *
 * To see what PHP settings are possible, including whether they can
 * be set at runtime (ie., when ini_set() occurs), read the PHP
 * documentation at http://www.php.net/manual/en/ini.php#ini.list
 * and take a look at the .htaccess file to see which non-runtime
 * settings are used there. Settings defined here should not be
 * duplicated there so as to avoid conflict issues.
 */
ini_set('arg_separator.output',     '&amp;');
ini_set('magic_quotes_runtime',     0);
ini_set('magic_quotes_sybase',      0);
ini_set('session.cache_expire',     200000);
ini_set('session.cache_limiter',    'none');
ini_set('session.cookie_lifetime',  2000000);
ini_set('session.gc_maxlifetime',   200000);
ini_set('session.save_handler',     'user');
ini_set('session.use_cookies',      1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid',    0);
ini_set('url_rewriter.tags',        '');
ini_set('display_errors',1);
//gemini
set_time_limit(3600);
//

/**
 * If you encounter a situation where users post a large amount of text, and
 * the result is stripped out upon viewing but can still be edited, Drupal's
 * output filter may not have sufficient memory to process it. If you
 * experience this issue, you may wish to uncomment the following two lines
 * and increase the limits of these variables. For more information, see
 * http://php.net/manual/en/pcre.configuration.php.
 */
# ini_set('pcre.backtrack_limit', 200000);
# ini_set('pcre.recursion_limit', 200000);

/**
 * Drupal automatically generates a unique session cookie name for each site
 * based on on its full domain name. If you have multiple domains pointing at
 * the same Drupal site, you can either redirect them all to a single domain
 * (see comment in .htaccess), or uncomment the line below and specify their
 * shared base domain. Doing so assures that users remain logged in as they
 * cross between your various domains.
 */
# $cookie_domain = 'example.com';

/**
 * Variable overrides:
 *
 * To override specific entries in the 'variable' table for this site,
 * set them here. You usually don't need to use this feature. This is
 * useful in a configuration file for a vhost or directory, rather than
 * the default settings.php. Any configuration setting from the 'variable'
 * table can be given a new value. Note that any values you provide in
 * these variable overrides will not be modifiable from the Drupal
 * administration interface.
 *
 * Remove the leading hash signs to enable.
 */
# $conf = array(
#   'site_name' => 'My Drupal site',
#   'theme_default' => 'minnelli',
#   'anonymous' => 'Visitor',
/**
 * A custom theme can be set for the off-line page. This applies when the site
 * is explicitly set to off-line mode through the administration page or when
 * the database is inactive due to an error. It can be set through the
 * 'maintenance_theme' key. The template file should also be copied into the
 * theme. It is located inside 'modules/system/maintenance-page.tpl.php'.
 * Note: This setting does not apply to installation and update pages.
 */
#   'maintenance_theme' => 'minnelli',
/**
 * reverse_proxy accepts a boolean value.
 *
 * Enable this setting to determine the correct IP address of the remote
 * client by examining information stored in the X-Forwarded-For headers.
 * X-Forwarded-For headers are a standard mechanism for identifying client
 * systems connecting through a reverse proxy server, such as Squid or
 * Pound. Reverse proxy servers are often used to enhance the performance
 * of heavily visited sites and may also provide other site caching,
 * security or encryption benefits. If this Drupal installation operates
 * behind a reverse proxy, this setting should be enabled so that correct
 * IP address information is captured in Drupal's session management,
 * logging, statistics and access management systems; if you are unsure
 * about this setting, do not have a reverse proxy, or Drupal operates in
 * a shared hosting environment, this setting should be set to disabled.
 */
#   'reverse_proxy' => TRUE,
/**
 * reverse_proxy accepts an array of IP addresses.
 *
 * Each element of this array is the IP address of any of your reverse
 * proxies. Filling this array Drupal will trust the information stored
 * in the X-Forwarded-For headers only if Remote IP address is one of
 * these, that is the request reaches the web server from one of your
 * reverse proxies. Otherwise, the client could directly connect to
 * your web server spoofing the X-Forwarded-For headers.
 */
#   'reverse_proxy_addresses' => array('a.b.c.d', ...),
# );

/**
 * String overrides:
 *
 * To override specific strings on your site with or without enabling locale
 * module, add an entry to this list. This functionality allows you to change
 * a small number of your site's default English language interface strings.
 *
 * Remove the leading hash signs to enable.
 */
# $conf['locale_custom_strings_en'] = array(
#   'forum'      => 'Discussion board',
#   '@count min' => '@count minutes',
# );
