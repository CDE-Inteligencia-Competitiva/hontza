<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?><?php print my_class_primera_proyecto($node,$page);?>">
<?php //if($page):?>
    <?php $my_user=user_load($node->uid);?>
    <div class="meta">
        <?php if ($submitted): ?>
        <p><?php print $submitted; ?></p>
      <?php endif; ?>
      <?php if ($terms): ?>
        <div class="terms">
          <span class="icon">&nbsp;</span>
          <?php print $terms; ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="content clearfix">
        <div class="field field-type-content-taxonomy field-field-categoria-servicios">
            <div class="field-label"><?php print t('Category');?>:&nbsp;</div>
            <div class="field-items">
            <?php $kont=0;?>
            <?php if(isset($node->taxonomy) && !empty($node->taxonomy)):?>
                <?php foreach($node->taxonomy as $id_taxo=>$taxo):?>
                    <?php if(($kont % 2)==0):?>
                    <div class="field-item odd">
                    <?php else:?>
                    <div class="field-item even">
                    <?php endif;?>
                        <?php //print $taxo->name;?>
                        <?php print get_term_extra_name($id_taxo, '', $taxo->name);?>
                    </div>
                    <?php $kont++;?>
                <?php endforeach;?>
            <?php endif;?>
            </div>
        </div>
        <div class="field field-type-text field-field-descripcion-servicios">
            <div class="field-label"><?php print t('Description');?>:&nbsp;</div>
            <div class="field-items">
                <?php $kont=0;?>
                <?php if(isset($node->field_descripcion_servicios) && !empty($node->field_descripcion_servicios)):?>
                    <?php foreach($node->field_descripcion_servicios as $id_desc=>$desc):?>
                        <?php if(($kont % 2)==0):?>
                        <div class="field-item odd">
                        <?php else:?>
                        <div class="field-item even">
                        <?php endif;?>
                            <p><?php print $desc['view'];?></p>
                        </div>
                        <?php $kont++;?>
                    <?php endforeach;?>
                <?php endif;?>
            </div>
        </div>
        <div class="field field-type-filefield field-field-logo-servicios">
            <div class="field-label"><?php print t('Logotype');?>:&nbsp;</div>
                <?php $kont=0;?>
                <?php if(isset($node->field_logo_servicios) && !empty($node->field_logo_servicios)):?>
                    <?php foreach($node->field_logo_servicios as $id_logo=>$logo):?>
                        <?php if(($kont % 2)==0):?>
                        <div class="field-item odd">
                        <?php else:?>
                        <div class="field-item even">
                        <?php endif;?>
                            <p><?php echo $logo['view']?></p>
                        </div>
                        <?php $kont++;?>
                    <?php endforeach;?>
               <?php endif;?>
            </div>
        </div>
        <div class="field field-type-text field-field-sitio-web">
            <div class="field-label"><?php print t('Website');?>:&nbsp;</div>
                <?php $kont=0;?>
                <?php if(isset($node->field_sitio_web) && !empty($node->field_sitio_web)):?>
                    <?php foreach($node->field_sitio_web as $id_sitio=>$sitio):?>
                        <?php if(($kont % 2)==0):?>
                        <div class="field-item odd">
                        <?php else:?>
                        <div class="field-item even">
                        <?php endif;?>
                            <p><?php echo $sitio['view']?></p>
                        </div>
                        <?php $kont++;?>
                    <?php endforeach;?>
                <?php endif;?>
        </div>
        <div class="field field-type-text field-field-sitio-web">
            <div class="field-label"><?php print t('Contact person');?>:&nbsp;</div>
                <?php $kont=0;?>
                <?php if(isset($node->field_persona_de_contacto) && !empty($node->field_persona_de_contacto)):?>
                    <?php foreach($node->field_persona_de_contacto as $id_persona=>$persona):?>
                        <?php if(($kont % 2)==0):?>
                        <div class="field-item odd">
                        <?php else:?>
                        <div class="field-item even">
                        <?php endif;?>
                            <p><?php echo $persona['view']?></p>
                        </div>
                        <?php $kont++;?>
                    <?php endforeach;?>
                <?php endif;?>
        </div>
        <div class="field field-type-text field-field-email">
            <div class="field-label"><?php print t('Email');?>:&nbsp;</div>
                <?php $kont=0;?>
                <?php if(isset($node->field_email) && !empty($node->field_email)):?>
                    <?php foreach($node->field_email as $id_email=>$email):?>
                        <?php if(($kont % 2)==0):?>
                        <div class="field-item odd">
                        <?php else:?>
                        <div class="field-item even">
                        <?php endif;?>
                            <p><?php echo $email['view']?></p>
                        </div>
                        <?php $kont++;?>
                    <?php endforeach;?>
                <?php endif;?>
        </div>
         <div class="field field-type-text field-field-telefono">
            <div class="field-label"><?php print t('Phone');?>:&nbsp;</div>
                <?php $kont=0;?>
                <?php if(isset($node->field_telefono) && !empty($node->field_telefono)):?>
                    <?php foreach($node->field_telefono as $id_telefono=>$telefono):?>
                        <?php if(($kont % 2)==0):?>
                        <div class="field-item odd">
                        <?php else:?>
                        <div class="field-item even">
                        <?php endif;?>
                            <p><?php echo $telefono['view']?></p>
                        </div>
                        <?php $kont++;?>
                    <?php endforeach;?>
               <?php endif;?>
        </div>
        <?php print hontza_red_compartir_facilitador_link($node);?>    
        <!--
        <div class="links">
            <?php //print $links;?>
        </div>
        -->
    </div>   
<?php //else:?>
    <?php //print t('Hay que implementar la parte else');?>
<?php //endif;?>
</div>