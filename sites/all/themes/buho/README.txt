Please read this file in its entirety before installing this theme.

#WARNINGS#

1. USE A SUB-THEME, IT'S FOR YOUR OWN GOOD
    If you plan on making changes, I strongly recommend that you
    create a sub-theme.  Creating a sub-theme is by far the best way
    to ensure your changes will not be overridden. It is also invaluable
    when it comes to debugging because you can easily see determine if
    there are bugs with the Sky theme or with your code.  If you don't
    know how to create one, see this page: http://drupal.org/node/225125

2. ENABLING TRANSPARENCY SUPPORT FOR IE6
    The Sky theme comes with transparency support for Internet Explorer
    6 using IEPNGFIX.HTC (http://www.twinhelix.com/css/iepngfix). You
    will need to modify the CSS, to include absolute paths from your
    site root to the Sky theme directory. Absolute paths are required
    and as I do not know which directory you are going to install in,
    you'll have to edit this manually.  Scroll down for detailed
    instructions on exactly how to do this, if you are unsure.

#INSTALLATION#

1. Choose a directory for your themes.  It's strongly recommended that
you do NOT install this theme in the Drupal theme directory (the directory
that contains Garland), because this is considered "hacking core" and
because when you upgrade Drupal itself, you risk overwriting any changes
you made, or on a Mac, loosing the theme entirely.  It is recommended that
you install the theme in one of the following directories:
    * /sites/all/themes/sky
    * /sites/default/themes/sky
    * /sites/mysite/themes/sky (if you are running a multi-site setup)

2. Visit the admin/build/themes page, and enable and set the Sky theme
to the default theme.

3. Not that the theme is enabled, visit the theme settings page, by
clicking the "configure" on the admin/build/themes page.  There you will
find quite a few settings that you can customize. 

#TROUBLESHOOTING#

* Make sure you have the files directory has been created and has the
correct permissions assigned. Drupal should take care of this during the
installation process, but if you are getting errors that Drupal cannot
create the custom.css file, it's likely due to permissions issues or the
lack of a files directory.
