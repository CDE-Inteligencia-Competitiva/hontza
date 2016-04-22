<?php

/**
 * @file node.tpl.php
 *
 * Theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: Node body or teaser depending on $teaser flag.
 * - $picture: The authors picture of the node output from
 *   theme_user_picture().
 * - $date: Formatted creation date (use $created to reformat with
 * - $links: Themed links like "Read more", "Add new comment", etc. output
 *   from theme_links().
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct url of the current node.
 * - $terms: the themed list of taxonomy term links output from theme_links().
 * - $submitted: themed submission information output from
 *   theme_node_submitted().
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type, i.e. story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $teaser: Flag for the teaser state.
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 */
?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?> clear-block">

<?php print $picture ?>
<?php if (!$page): ?>
  <h2><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h2>
<?php endif; ?>

<!--
  <div class="meta">
  <?php //if ($submitted): ?>
    <span class="submitted"><?php //print $submitted ?></span>
  <?php //endif; ?>
-->  
   
  <div class="content">
    <?php $my_user_info=my_get_user_info($node);?>
 
	  <div style="float:left;clear:both;">
			<?php print $my_user_info['img'];?> 
	  </div>
	
	  <div class="field field-type-text field-field-fuentehtml-created" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Creation Date');?>:&nbsp;
				</div>
				<?php print date('d-m-Y H:i',$node->created); ?>
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-fuentehtml-url" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Url');?>:&nbsp;
				</div>
                                <?php print $node->field_fuentehtml_fuente[0]['value'];?>
			</div>
		</div>
	  </div>
  
          <div class="field field-type-text field-field-fuentehtml-minimun_words_in_the_title" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Minimum words in the title');?>:&nbsp;
				</div>
				<?php print $node->field_titulo_word_min[0]['value'];?>
			</div>
		</div>
	  </div>	
  	  
      
          <div class="field field-type-text field-field-fuentehtml-quality" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Quality');?>:&nbsp;
				</div>
				<?php print hontza_get_fuente_stars_view($node,'field_fuentehtml_calidad');?>
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-fuentehtml-coverage" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Coverage');?>:&nbsp;
				</div>
				<?php print hontza_get_fuente_stars_view($node,'field_fuentehtml_exhaustividad');?>
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-fuentehtml-update" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Update');?>:&nbsp;
				</div>
				<?php print hontza_get_fuente_stars_view($node,'field_fuentehtml_actualizacion');?>
			</div>
		</div>
	  </div>
      
	  	 	  
          <?php include('source_api_view_icon.tpl.php');?>
      
	   																		    
  </div>
   
  <div style="float:left;clear:both;padding-top:10px;">
        
	<?php print $links; ?>
  </div>
                <?php if(hontza_is_con_botonera()):?>	
		  <div class="n-opciones-item">
                                

  				<div class="item-comentar">
					<?php print hontza_fuente_comment_link($node);?>
                                </div>
      			
                                <div class="item-editar">
					 <?php print hontza_fuente_edit_link($node);?>
                                </div>
    
	  			<div class="item-borrar">
                                    <?php print hontza_fuente_delete_link($node);?>
	  			</div>
                                <!--
	  			<div class="items-coments-page0">
        			<?php //print my_get_node_c_d_w($node)?>
	  			</div>
                                -->
                </div><!-- opciones-item-->
                <?php endif;?>    
</div>
