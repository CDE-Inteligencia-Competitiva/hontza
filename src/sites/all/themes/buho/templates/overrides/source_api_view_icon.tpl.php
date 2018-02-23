          <?php //gemini-2013 ?>     
          <div class="field field-type-text field-alchemy-icon" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Apply Alchemy');?>:&nbsp;
				</div>
				<?php print my_get_source_view_api_icon($node,'alchemy')?>      
			</div>
		</div>
	  </div>
                    
          <?php //gemini-2013 ?>      
          <div class="field field-type-text field-opencalais-icon" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Apply OpenCalais');?>:&nbsp;
				</div>
				<?php print my_get_source_view_api_icon($node,'opencalais')?>      
			</div>
		</div>
	  </div>
          <?php //intelsat-2016?>
          <?php if(!in_array($node->type,array('canal_de_supercanal','canal_de_yql'))):?>
          <?php //gemini-2013 ?>  
          <div class="field field-type-text field-fulltextrss-icon" style="float:left;clear:both;">
		<div class="field-items">
			<div class="field-item odd">
				<div class="field-label-inline-first">
				  <?php print t('Apply FulltextRSSFeed');?>:&nbsp;
				</div>
				<?php print my_get_source_view_api_icon($node,'full_text_rss')?>      
			</div>
		</div>
	  </div>
          <?php endif;?>