<?php //print $node->body;?>
<?php //echo print_r($node,1);exit();?>
<?php //print theme_node_submitted($node)?>
<?php //print $node->content['body']['#value'];exit();?>
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
  <?php if(red_is_show_source_title()):?>  
  <h2><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h2>
  <?php endif; ?>
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
      
          <?php if(!red_is_show_source_title()):?>
          <div class="field field-type-text field-field-fuentedapper-creator" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('User');?>:&nbsp;
				</div>
				<?php print $my_user_info['username'];?> 
			</div>
		</div>
	  </div>
          <?php endif?>
	
	  <div class="field field-type-text field-field-fuentedapper-created" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Creation Date');?>:&nbsp;
				</div>
				<?php print date('d-m-Y H:i',$node->created); ?>
			</div>
		</div>
	  </div>	
  
  	  <div class="field field-type-text field-field-fuentedapper-clasificaciones" style="float:left;clear:both;">
		<div class="field-items">			
			<div class="field-item odd">			
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Type');?>:&nbsp;
				</div>					
				  <?php if ($terms): ?>
                                        <?php $terms=translate_html_terms($terms);?>
                                        <?php if(!red_is_show_source_title()):?>
                                            <?php $terms=strip_tags($terms,'<ul><li>'); ?>
                                        <?php endif;?>
					<div class="terms terms-inline" style="margin-top:0px;"><?php print $terms ?></div>
				  <?php endif;?>
			</div>
		</div>
	  </div>
	  
	  <?php //echo print_r($node,1);?>
	  
	  <?php $my_node=node_load($node->nid)?>
	  
	  <div class="field field-type-text field-field-fuentedapper-resumen" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Summary');?>:&nbsp;
				</div>									
				<div class="my_div_body">
					<?php print $my_node->body;?>  
				</div>
			</div>
		</div>
	  </div>
	  
	  <div class="field field-type-text field-field-fuentedapper-fuente" style="float:left;clear:both;padding-top:10px;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Source');?>:&nbsp;
				</div>
				<?php if(count($node->field_fuentedapper_fuente>0)):?>
					<?php foreach($node->field_fuentedapper_fuente as $i=>$fuente):?>
						<?php print $fuente['value'];?>&nbsp; 
					<?php endforeach;?>
				<?php endif;?>        
			</div>
		</div>
	  </div>
	  	  	  
		<div class="field field-type-text field-field-fuentedapper-extraargs" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('extraArgs');?>:&nbsp;
				</div>
				<div style="float:left;">
					<ul class="my_fuentedapper_ul">
					<?php if(count($node->field_fuentedapper_extraargs>0)):?>
						<?php foreach($node->field_fuentedapper_extraargs as $i=>$my_extra):?>
							<li class="my_fuentedapper_li"><?php print $my_extra['value'];?>&nbsp;</li> 
						<?php endforeach;?>
					<?php endif;?>
					</ul>
				</div>        
			</div>
		</div>
	  </div>	  
			  
	   <div class="field field-type-text field-field-fuentedapper-args" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Arguments');?>:&nbsp;
				</div>
				<div style="float:left;">
					<ul class="my_fuentedapper_ul">
					<?php if(count($node->field_fuentedapper_args>0)):?>
						<?php foreach($node->field_fuentedapper_args as $i=>$my_args):?>
							<li class="my_fuentedapper_li"><?php print $my_args['value'];?>&nbsp;</li> 
						<?php endforeach;?>
					<?php endif;?>
					</ul>
				</div>        
			</div>
		</div>
	  </div>
	  
	    <div class="field field-type-text field-field-fuentedapper-args-desc" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Description of parameters');?>:&nbsp;
				</div>
				<div style="float:left;">
					<ul class="my_fuentedapper_ul">
						<?php if(count($node->field_fuentedapper_args_desc>0)):?>
							<?php foreach($node->field_fuentedapper_args_desc as $i=>$my_args_desc):?>
								<li class="my_fuentedapper_li"><?php print $my_args_desc['value'];?>&nbsp;</li>  
							<?php endforeach;?>
						<?php endif;?>
					</ul>
				</div>		        
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-fuentedapper-quality" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Quality');?>:&nbsp;
				</div>
				<?php print hontza_get_fuente_stars_view($node,'field_fuentedapper_calidad');?>
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-fuentedapper-coverage" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Coverage');?>:&nbsp;
				</div>
				<?php print hontza_get_fuente_stars_view($node,'field_fuentedapper_exhaustividad');?>
			</div>
		</div>
	  </div>
      
          <div class="field field-type-text field-field-supercanal-update" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first" style="float:left;">
				  <?php print t('Update');?>:&nbsp;
				</div>
				<?php print hontza_get_fuente_stars_view($node,'field_fuentedapper_actualizacion');?>
			</div>
		</div>
	  </div>
	  	 	  
          <?php include('source_api_view_icon.tpl.php');?>
      
	   <div class="field field-type-text field-tematica-gupos" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Groups');?>:&nbsp;
				</div>
				<?php if(count($node->field_tematica_gupos>0)):?>
					<?php foreach($node->field_tematica_gupos as $i=>$gu):?>
						<?php print $gu['value'];?>&nbsp; 
					<?php endforeach;?>
				<?php endif;?>        
			</div>
		</div>
	  </div>																			
  
    <?php //gemini ?>    
	<?php //print $content ?>		
	<?php //$my_content=str_replace($node->content['body']['#value'],'',$content);?>
	<?php //print $my_node->body;?>
	<?php //print $my_content;?>
  </div>
  <!--  
  <?php //if(red_is_show_source_title()):?> 
  <div style="float:left;clear:both;padding-top:10px;">
	<?php //print $links; ?>
  </div>
  -->
  <?php //endif;?>
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