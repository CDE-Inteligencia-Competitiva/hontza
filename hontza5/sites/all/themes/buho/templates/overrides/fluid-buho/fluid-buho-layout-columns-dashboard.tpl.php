    <div id='c-left' style="width:33%;margin-left:5px;float:left;">
        <div class='page-region' style="width:100%;">
            <?php print my_get_grupo_descripcion_region();?>
        </div>
        <div class='page-region' style="width:100%">
            <?php print red_funciones_get_preguntas_clave_inicio();?>
        </div>
        <div class='page-region' style="width:100%">
            <?php print calendario_get_region();?>
        </div>        
    </div>
    <div id='c-center' style="width:33%;margin-left:5px;float:left;">
        <div class='page-region' style="width:100%">
            <?php print red_funciones_get_noticias_validadas_inicio();?>
        </div>          
    </div>
    <div id='c-right' style="width:33%;margin-left:5px;float:left;">
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