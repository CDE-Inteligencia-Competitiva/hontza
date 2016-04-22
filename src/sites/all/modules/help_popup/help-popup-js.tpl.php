<?php
  //  Template for help popup window
  //  Parameters include 
  //  $node which is the complete node object including:
  //  $node->height (window height)
  //  $node->width (window width)
?>
  <div id="help_popup">
    <div id="title">
      <?php if ($node->title): print '<h2>'. $node->title .'</h2>'; endif; ?>
 	</div>
	<div id="content">
	  <?php print $node->body?>
	</div>
	<div class="closewindow">
	  <input class="popup-button jqmClose" type="button" value="Close window" />
	</div>
  </div> <!--// Popup ends -->