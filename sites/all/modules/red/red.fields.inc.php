<?php
function red_fields_inc_get_field_item_web_name(){
    if(defined('_FIELD_RED_ITEM_WEB_NAME')){
        $result=_FIELD_RED_ITEM_WEB_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_field_idiomas_name(){
    if(defined('_FIELD_RED_IDIOMAS_NAME')){
        $result=_FIELD_RED_IDIOMAS_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_get_no_existe_field();
}
function red_fields_inc_get_field_periodicidad_name(){    
    if(defined('_FIELD_RED_PERIODICIDAD_NAME')){
        $result=_FIELD_RED_PERIODICIDAD_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_get_no_existe_field();
}
function red_fields_inc_get_no_existe_field(){
    return 'no_existe_field';
}
function red_fields_inc_get_field_url_name(){
    if(defined('_FIELD_RED_URL_NAME')){
        $result=_FIELD_RED_URL_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_field_country_fuente_name(){
    if(defined('_FIELD_RED_COUNTRY_FUENTE_NAME')){
        $result=_FIELD_RED_COUNTRY_FUENTE_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_field_fuente_region_name(){
    if(defined('_FIELD_RED_FUENTE_REGION_NAME')){
        $result=_FIELD_RED_FUENTE_REGION_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_field_red_fecha_noticia_name(){
    if(defined('_FIELD_RED_FECHA_NOTICIA_NAME')){
        $result=_FIELD_RED_FECHA_NOTICIA_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_field_boletines_name(){
    if(defined('_FIELD_RED_BOLETINES_NAME')){
        $result=_FIELD_RED_BOLETINES_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_field_visitas_name(){
    if(defined('_FIELD_RED_VISITAS_NAME')){
        $result=_FIELD_RED_VISITAS_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_field_resumen_name(){
    if(defined('_FIELD_RED_RESUMEN_NAME')){
        $result=_FIELD_RED_RESUMEN_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_field_red_sectorizacion_name(){
    if(defined('_FIELD_RED_SECTORIZACION_NAME')){
        $result=_FIELD_RED_SECTORIZACION_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_field_red_cnae_name(){
    if(defined('_FIELD_RED_CNAE_NAME')){
        $result=_FIELD_RED_CNAE_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_field_red_fuente_noticia_name(){
    if(defined('_FIELD_RED_FUENTE_NOTICIA_NAME')){
        $result=_FIELD_RED_FUENTE_NOTICIA_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_content_field_red_fecha_noticia_table_name(){
    if(defined('_CONTENT_FIELD_RED_FECHA_NOTICIA_NAME')){
        $result=_CONTENT_FIELD_RED_FECHA_NOTICIA_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_content_field_red_fecha_noticia_value(){
    if(defined('_FIELD_RED_FECHA_NOTICIA_VALUE')){
        $result='_FIELD_RED_FECHA_NOTICIA_VALUE';
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_field_country_name(){
    if(defined('_FIELD_RED_COUNTRY_NAME')){
        $result=_FIELD_RED_COUNTRY_NAME;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}
function red_fields_inc_get_field_idioma_principal_name(){
    if(defined('_FIELD_RED_IDIOMA_PRINCIPAL')){
        $result=_FIELD_RED_IDIOMA_PRINCIPAL;
        if(!empty($result)){
            return $result;
        }        
    }
    return red_fields_inc_get_no_existe_field();
}