<?php include 'page.header.inc'; ?>
<?php if(is_dashboard()):?>
    <?php if(red_funciones_is_tema_fluid_buho()):?>
        <?php include(red_funciones_get_fluid_buho_templates_dir().'fluid-buho-layout-columns-dashboard.tpl.php');?>
    <?php else:?>
    <div id='c-left' style="width:31%">
        <div class='page-region' style="width:100%">
            <?php print my_get_grupo_descripcion_region();?>
        </div>
        <div class='page-region' style="width:100%">
            <?php print red_funciones_get_preguntas_clave_inicio();?>
        </div>
        <div class='page-region' style="width:100%">
            <?php print calendario_get_region();?>
        </div>        
    </div>
    <div id='c-center' style="width:31%;margin-left:0px;">
        <div class='page-region' style="width:100%">
            <?php print red_funciones_get_noticias_validadas_inicio();?>
        </div>          
    </div>
    <div id='c-right' style="width:31%;margin-left:0px;">
        <div class='page-region' style="width:100%">
            <?php print red_funciones_get_respuestas_inicio();?>
        </div>
        <div class='page-region' style="width:100%">
            <?php print boletin_report_get_boletines_historicos_inicio();?>
        </div>
        <div class='page-region' style="width:100%">
            <?php print red_funciones_get_wikis_inicio();?>
        </div>
        <div class='page-region' style="width:100%">
            <?php print red_funciones_get_debates_inicio();?>
        </div>
        <div class='page-region' style="width:100%">
            <?php print red_funciones_get_nube_de_etiquetas_inicio();?>
        </div>  
    </div>
    <?php endif;?>
<?php else:?>
<?php if(red_funciones_is_tema_fluid_buho()):?>
    <?php include(red_funciones_get_fluid_buho_templates_dir().'fluid-buho-no-dash.tpl.php');?>
<?php else:?>
<div id='c-left'>
    <div class='page-region'>
        <?php if ($homeleft) print $homeleft ?>
    </div>
</div>
<div id='c-center'>
    <div class='page-region'>
        <?php if ($homecenter) print $homecenter ?>
    </div>
</div>
<div id='c-right'>
    <div class='page-region'>
        <?php if ($homeright) print $homeright ?>
    </div>
</div>
<?php endif;?>
<?php endif;?>
<?php include 'page.footer.inc'; ?>