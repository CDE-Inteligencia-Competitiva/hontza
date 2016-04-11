<?php

/**
 * @file user-profile.tpl.php
 * Default theme implementation to present all user profile data.
 *
 * This template is used when viewing a registered member's profile page,
 * e.g., example.com/user/123. 123 being the users ID.
 *
 * By default, all user profile data is printed out with the $user_profile
 * variable. If there is a need to break it up you can use $profile instead.
 * It is keyed to the name of each category or other data attached to the
 * account. If it is a category it will contain all the profile items. By
 * default $profile['summary'] is provided which contains data on the user's
 * history. Other data can be included by modules. $profile['user_picture'] is
 * available by default showing the account picture.
 *
 * Also keep in mind that profile items and their categories can be defined by
 * site administrators. They are also available within $profile. For example,
 * if a site is configured with a category of "contact" with
 * fields for of addresses, phone numbers and other related info, then doing a
 * straight print of $profile['contact'] will output everything in the
 * category. This is useful for altering source order and adding custom
 * markup for the group.
 *
 * To check for all available data within $profile, use the code below.
 * @code
 *   print '<pre>'. check_plain(print_r($profile, 1)) .'</pre>';
 * @endcode
 *
 * Available variables:
 *   - $user_profile: All user profile data. Ready for print.
 *   - $profile: Keyed array of profile categories and their items or other data
 *     provided by modules.
 *
 * @see user-profile-category.tpl.php
 *   Where the html is handled for the group.
 * @see user-profile-item.tpl.php
 *   Where the html is handled for each item in the group.
 * @see template_preprocess_user_profile()
 */
?>
<div class="profile">
  <?php //gemini?>	
  <?php //print $user_profile; ?>
  <?php if(is_normal_user_profile($profile)):?>
        <?php if(is_user_invitado()):?>
            <?php $user_profile=get_invitado_user_profile($user_profile)?>
  	<?php endif;?>
        <?php if(hontza_canal_rss_is_usuario_ficha_tabla()):?>
          <table class="table_node_view">
            <?php print $user_profile; ?>
                <?php $my_info=my_show_other_user_profile();?>
                <tr class="tr_node_view">
                    <td class="td_label_node_view"><b><?php print t('Groups');?></b><!--:--> </td>
                    <td class="td_value_node_view">
                        <div class="item-list">
						<ul>
							<?php print $my_info['groups_li'];?>
						</ul>
					</div>
                    </td>
                </tr>    
            </table>    
        <?php else:?>            
            <?php print $user_profile; ?>
        <?php endif;?>    
  <?php else:?>
    <?php $my_info=my_show_other_user_profile();?>
	<?php if($my_info['with_info']):?>
		<?php print $profile['user_picture'];?>
                <?php if(hontza_canal_rss_is_usuario_ficha_tabla()):?>
                    <?php include('user-profile-other-user-table-view.tpl.php');?>
                <?php else:?>
                <!--
		</div>
		-->
		<h3><?php print t('Organisation');?></h3>
		<dl>
  			<dt class="profile-profile_empresa"><?php print t('Organisation');?></dt>
			<dd class="profile-profile_empresa"><?php print $my_info['profile_empresa'];?></dd>
		</dl>
		<h3><?php print t('Personal data');?></h3>
		<dl>
		  	<dt class="profile-profile_nombre"><?php print t('Name');?></dt>
			<dd class="profile-profile_nombre"><?php print $my_info['profile_nombre'];?></dd>
			<dt class="profile-profile_apellidos"><?php print t('Surname');?></dt>
			<dd class="profile-profile_apellidos"><?php print $my_info['profile_apellidos'];?></dd>
		</dl>
		<h3><?php print t('History');?></h3>
                <dl class="user-member">
  			<div class="form-item form-item-item">
 				<label><?php print t('Groups');?>: </label>
 					<div class="item-list">
						<ul>
							<?php print $my_info['groups_li'];?>
						</ul>
					</div>
			</div>
			<dt><?php print t('Member for');?></dt>
			<dd><?php print $my_info['member_for'];?></dd>
		</dl>
                <?php endif;?>
	<?php else:?>
                <?php print $user_profile; ?>
	<?php endif;?>
  <?php endif;?>
</div>