<div class="field field-type-text field-field-item-collection-resource-id" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Resource id');?>:&nbsp;
					</div>									
					<?php print social_learning_items_get_item_resources_id($node);?>  
				</div>
			</div>
</div>
<div class="field field-type-text field-field-item-collection-interest" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print t('Interest');?>:&nbsp;
					</div>									
					<?php print social_learning_items_get_item_interest_value($node); ?>  
				</div>
			</div>
</div>
<div class="field field-type-text field-field-item-collection-score_average" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print 'Score Average';?>:&nbsp;
					</div>									
					<?php print social_learning_items_get_item_score_average_value($node); ?>  
				</div>
			</div>
</div>
<div class="field field-type-text field-field-item-collection-tags" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
					<div class="field-label-inline-first" style="float:left;">
					  <?php print 'Social Learning Tags';?>:&nbsp;
					</div>									
					<?php print social_learning_items_get_item_tags_value_html($node); ?>  
				</div>
			</div>
</div>
 <div class="field field-type-text field-field-item-collection-topics" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
                                        <!--
					<div class="field-label-inline-first" style="float:left;">
                                        -->
                                        <div class="field-label-inline-first">
					  <?php print t('Topics');?>:&nbsp;
					</div>									
					<?php print social_learning_items_get_item_topics_value_html_table($node); ?>  
				</div>
			</div>
</div>
<div class="field field-type-text field-field-item-collection-mentions" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
                                        <!--
					<div class="field-label-inline-first" style="float:left;">
                                        -->
                                        <div class="field-label-inline-first">
					  <?php print t('Mentions');?>:&nbsp;
					</div>									
					<?php print social_learning_items_get_item_mentions_value_html_table($node); ?>  
				</div>
			</div>
</div>
<div class="field field-type-text field-field-item-collection-server-status" style="float:left;clear:both;">
			<div class="field-items">
				<div class="field-item odd">
                                        <!--
					<div class="field-label-inline-first" style="float:left;">
                                        -->
                                        <div class="field-label-inline-first">
					  <?php print t('Server Status');?>:&nbsp;
					</div>									
					<?php print social_learning_items_get_item_collection_server_status_label($node); ?>  
				</div>
			</div>
</div>