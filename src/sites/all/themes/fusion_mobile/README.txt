WHAT IS FUSION MOBILE?
----------------------

Fusion Mobile is a Fusion Core subtheme designed for creating a custom theme 
targeting mobile devices. By using a separate subtheme, you can control your 
theme settings and block layout/contents specifically for mobile devices.


FEATURES
--------

 * Simple region layout in a single column
 * Fluid layout supports a variety of devices/orientations
 * Works on devices down to a 300px wide screen
 * Skinr/Fusion integration for (simplified) layout controls in Drupal's UI
 * Built in support for touch icons (Apple and Android devices)
 * Uses a region for primary menu so you can customize your menu on mobile
 * Prevents images from overflowing screen width
 * Includes helpful CSS skeleton for fast theming
 * Supported by the awesome TopNotchThemes team :)


INSTALLATION
------------

 1. Download Fusion from http://drupal.org/project/fusion

 2. Download Fusion Mobile from http://drupal.org/project/fusion_mobile

 3. Unpack the downloaded files, take the folders and place them in your
    Drupal installation under one of the following locations:
      sites/all/themes
        making it available to the default Drupal site and to all Drupal sites
        in a multi-site configuration
      sites/default/themes
        making it available to only the default Drupal site
      sites/example.com/themes
        making it available to only the example.com site if there is a
        sites/example.com/settings.php configuration file

    Note: you will need to create the "themes" folder under "sites/all/"
    or "sites/default/".

 4. Install the Skinr module: http://drupal.org/project/skinr
    Skinr makes Fusion even more powerful, giving you control over Fusion's 
    layout and style options in Drupal's interface. Download and install 
    this module like usual to get the most out of Fusion.

	* How to install modules: http://drupal.org/node/70151

 5. Follow the instructions below to build your own Fusion Mobile theme.

 6. We recommend the Mobile Tools module to switch to your mobile theme when 
    a mobile device is detected:
    http://drupal.org/project/mobile_tools

        Mobile Tools also provides integration with other modules to change 
        your site's URL, node displays, settings, Panels, permissions and 
        more when browsing with a mobile device.


FURTHER READING
---------------

Full documentation on using Fusion:
  http://fusiondrupalthemes.com/support/documentation

Full documentation on creating a custom Fusion subtheme:
  http://fusiondrupalthemes.com/support/theme-developers

Drupal theming documentation in the Theme Guide:
  http://drupal.org/theme-guide


BUILD YOUR OWN SUBTHEME
-----------------------

*** IMPORTANT ***

* If add a new template (.tpl.php) file to your subtheme or modify any lines in 
your subtheme's .info file, you MUST refresh Drupal's cache by clicking the 
"Clear all caches" button at admin/config/development/performance.

* Alternately, you can install one of the following modules and use their cache 
clearing functions: 
    Administration menu module (http://drupal.org/project/admin_menu)
    Devel module (http://drupal.org/project/devel)


The Fusion Mobile theme is designed to be renamed and edited directly, as you 
would with Fusion Starter (in the main Fusion package).

The examples below assume Fusion and your subtheme will be in sites/all/themes/

 1. Rename the fusion_mobile folder to the name of your new subtheme. 
    IMPORTANT: Only lowercase letters and underscores should be used.

    For example, copy the sites/all/themes/fusion_mobile folder and rename it
    as sites/all/themes/sunshine_mobile.

 2. In your new subtheme folder, rename the .info file as the name of your 
    new subtheme. Then edit the .info file to update the name and description.

    For example, rename the fusion_mobile.info file to sunshine_mobile.info. 
    Edit the sunshine_mobile.info file and change "name = Fusion Mobile" to 
    "name = My Sunshine Mobile Theme" and the description to 
    "description = A mobile called Sunshine".

 3. On this line: stylesheets[all][] = css/fusion-mobile-style.css, replace the 
    "fusion-mobile" part with your theme's name. Rename the css file in the css/ 
    folder to match. 

    In our example, you would have a file at css/sunshine--mobile-style.css

    Then, visit your site's admin/appearance to set your new theme as the default.

 4. Visit your subtheme's settings page (click "Settings" next to it at 
    admin/appearance) to configure basic options and layout.

	* Learn more about .info file values and setting defaults for these theme
	  settings at: 
	  http://fusiondrupalthemes.com/support/theme-developers/subtheming-quickstart

    * NOTE: Theme settings for sidebars are not functional in the mobile version


Optional:

    MODIFYING TEMPLATE FILES:
    If you decide you want to modify any of the .tpl.php template files in the
    fusion_core folder, copy them to your subtheme's folder before making any 
    changes. Then rebuild the theme registry.

    For example, copy fusion_core/block.tpl.php to sunshine_mobile/block.tpl.php


APPLE TOUCH ICONS
-----------------

Fusion Mobile's page.tpl.php supports touch icons on Apple & Android devices. 
This enables a high resolution custom icon when mobile users bookmark your site.

To use this feature, place icons with the following names and dimensions in your 
theme's folder:

    114x114px: apple-touch-icon-114x114-precomposed.png
    72x72px:   apple-touch-icon-72x72-precomposed.png
    57x57px:   apple-touch-icon-precomposed.png

NOTE: If you want a gloss effect and rounded corners to be added automatically to 
your icon, edit the page.tpl.php to remove the "-precomposed" part of the 
filename, and rename your icon files to match.

Further reading on device support: http://mathiasbynens.be/notes/touch-icons