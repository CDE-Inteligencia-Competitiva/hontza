<div class="field field-type-text field-field_wiki_rss_name" style="float:left;clear:both;">
    <div class="field-items">
        <div class="field-item odd">
            <div class="field-label-inline-first" style="float:left;">
                <?php print t('Rss name');?>:&nbsp;
            </div>
                <?php print compartir_documentos_get_wiki_rss_name($node);?>
            </div>
	</div>
</div>
<div class="field field-type-text field-field_wiki_import_rss_url" style="float:left;clear:both;">
    <div class="field-items">
        <div class="field-item odd">
            <div class="field-label-inline-first" style="float:left;">
                <?php print t('Import rss url');?>:&nbsp;
            </div>
                <?php print compartir_documentos_get_wiki_import_rss_url($node);?>
            </div>
	</div>
</div>
<div class="field field-type-text field-field_wiki_is_shared" style="float:left;clear:both;">
    <div class="field-items">
        <div class="field-item odd">
            <div class="field-label-inline-first" style="float:left;">
                <?php print t('Exported');?>:&nbsp;
            </div>
                <?php print compartir_documentos_get_wiki_is_shared_string($node);?>
            </div>
	</div>
</div> 
<div class="field field-type-text field-field_wiki_is_imported" style="float:left;clear:both;">
    <div class="field-items">
        <div class="field-item odd">
            <div class="field-label-inline-first" style="float:left;">
                <?php print t('Imported');?>:&nbsp;
            </div>
                <?php print compartir_documentos_get_wiki_is_imported_string($node);?>
            </div>
	</div>
</div>
<div class="field field-type-text field-field_wiki_frecuencia" style="float:left;clear:both;">
    <div class="field-items">
        <div class="field-item odd">
            <div class="field-label-inline-first" style="float:left;">
                <?php print t('Frequency');?>:&nbsp;
            </div>
                <?php print compartir_documentos_get_wiki_frecuencia($node,1);?>
            </div>
	</div>
</div>
<div class="field field-type-text field-field_wiki_frecuencia_hora" style="float:left;clear:both;">
    <div class="field-items">
        <div class="field-item odd">
            <div class="field-label-inline-first" style="float:left;">
                <?php print t('Frequency hour');?>:&nbsp;
            </div>
                <?php print compartir_documentos_get_wiki_frecuencia_hora($node);?>
            </div>
	</div>
</div>
<div class="field field-type-text field-field_wiki_import_frecuencia" style="float:left;clear:both;">
    <div class="field-items">
        <div class="field-item odd">
            <div class="field-label-inline-first" style="float:left;">
                <?php print t('Import frequency');?>:&nbsp;
            </div>
                <?php print compartir_documentos_get_wiki_import_frecuencia($node,1);?>
            </div>
	</div>
</div>
<div class="field field-type-text field-field_wiki_import_frecuencia_hora" style="float:left;clear:both;">
    <div class="field-items">
        <div class="field-item odd">
            <div class="field-label-inline-first" style="float:left;">
                <?php print t('Import frequency hour');?>:&nbsp;
            </div>
                <?php print compartir_documentos_get_wiki_import_frecuencia_hora($node);?>
            </div>
	</div>
</div>