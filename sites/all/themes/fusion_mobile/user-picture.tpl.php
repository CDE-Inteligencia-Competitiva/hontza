<?php
// $Id: user-picture.tpl.php,v 1.2 2007/08/07 08:39:36 goba Exp $

/**
 * @file
 * Default theme implementation to present an picture configured for the
 * user's account.
 *
 * Available variables:
 * - $picture: Image set by the user or the site's default. Will be linked
 *   depending on the viewer's permission to view the users profile page.
 * - $account: Array of account information. Potentially unsafe. Be sure to
 *   check_plain() before use.
 *
 * @see template_preprocess_user_picture()
 */
?>
<?php //gemini ?>
<?php //if ($picture): ?>
<div class="picture">
<?php  if($account->picture):?> 
  <?php //print $picture; ?>
  <?php print my_get_user_img_src('',$account->picture,$account->name,$account->uid);?>
<?php else:?>
	<?php print my_get_user_img_src('','',$account->name,$account->uid);?>
<?php endif;?>
</div>