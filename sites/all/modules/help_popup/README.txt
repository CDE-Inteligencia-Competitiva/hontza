popup README.txt

INSTALL 

usual install process

copy the file popup.php to the drupal root.  This is essential since the popup runs as a separate php script and requires some drupal files to run.

CONFIGURATION

Popup comes with a default but you might like to create a popup template at admin/settings/popup.  Here you define the following:

linkobject - which is the html that forms the link that users click to get the popup, typically this might be a help icon
window options - the window parameters such as height, width etc
name - the name of the template so that you can call it correctly when required

USE

Popup comes with a function popup_help_window($nid, $template, $width, $height, $linkobject) which you can use to render a help link wherever you need one.  The arguments are as follows:

$nid - the nid of the node with the text that will be in the popup.  You might have a help content type which you use to create help text
$template - the name of the template you are using.  Defaults to the popup default
$height - option to customize the height for this node.  Defaults to template default
$width - option to customize width for this node.  Defaults to template default
$linkobject - option to customize the linkobject for this node.  Defaults to template default

ADDING TO MENUS

Menu systems are the ideal place to put popups, eg help popups.

To add a popup to a menu item in the Drupal menu User Interface, do the following:

Place this function (or similar) into your template.php theme file

function phptemplate_menu_item($link, $has_children, $menu = '', $in_active_trail = FALSE, $extra_class = NULL) {
  $class = ($menu ? 'expanded' : ($has_children ? 'collapsed' : 'leaf'));
  if (!empty($extra_class)) {
    $class .= ' ' . $extra_class;
  }
  if ($in_active_trail) {
    $class .= ' active-trail';
  }
  if (strpos($link, '[popup') && module_exists('help_popup')) {
    $replacements = help_popup_get_replacements($link);
	$link = str_replace($replacements['tag'], help_popup_window($replacements['nid'], 'help'), $link);
  }
  return '<li class="' . $class . '">' . $link . $menu . "</li>\n";
}

Use the following tag in any menu item label:
[popup-123]
Where 123 is the nid of the node that you want to appear in the popup.

eg My Account [popup-24] will render the My Account menu label with a popup icon next to it that opens node 24.

ADDING TO NODES

Help popup includes a node filter.  Simply activate it in a input filter of your choice and use the tage 
[popup-123]
Where 123 is the nid of the node that you want to appear in the popup.

ADDING TO FORMS

Help popups are quite handy in forms to give users that extra bit of assistance.

Using admin/settings/help_popup you can also add popups to any number of form fields provided that you can identify the form name and the form field and that the form array is no more than two elements deep.  ie the script will pick up $form['title'][#title'] and $form['body_field']['body']['#title'] but nothing deeper.  The linkobject will display as a prefix to the title and just needs a float:left in the css to get it nicely placed.

ADDING ANYWHERE

To add a popup anywhere in your site use:

print help_popup_window($nid, $template, $linkobject, $width, $height);

Where $nid is the node nid and $template is the template name as per settings.  The other three arguments are optional and are available to use if you want to over-ride the template settings. The linkobject is the html which the user clicks on to get the popup.

THEME TEMPLATE

The popup window markup is found in help_popup.tpl.php which is created through the drupal theme template system so can be copied to your theme and modified to suit.  The only variables available are the theme stylesheets and the node object which also includes $node->height (window height) and $node->width (window width)

