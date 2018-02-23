<?php
  //  Template for help popup window
  //  Parameters include 
  //  $styles which is markup for theme stylesheets
  //  $node which is the complete node object including:
  //  $node->height (window height)
  //  $node->width (window width)
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='en'>
  <head>
    <title><?php print $node->title ?></title>
    <?php print $styles ?>
	<style type="text/css">
	  #popup { width:<?php print $node->width;?>px;}
	</style>
    <?php print variable_get('my_scripts','');?>    
  </head>
  <body>
  <div id='popup'>
    <div id="title">
      <?php if ($node->title): print '<h2>'. $node->title .'</h2>'; endif; ?>
 	</div>
	<div id="content">
	  <?php print $node->body?>
	</div>
	<div class="closewindow">
          <!--  
	  <input class="popup-button" type="button" onclick="self.close();" value="Close window" />
          -->
          <input class="popup-button" type="button" onclick="window.close();" value="Close window" />
	</div>
  </div> <!--// Popup ends -->
</body>
</html>