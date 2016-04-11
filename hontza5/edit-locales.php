<?php
require_once('includes/bootstrap.inc');
//session_start();
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
global $user;
if(isset($user->uid) && !empty($user->uid) && $user->uid==1){
    //
}else{
    print 'access denied';
    exit();
}
if(isset($_POST['update_translations_btn'])){
    if(isset($_POST['translations'])){
        edit_locales_save_translations($_POST['translations']);
    }
}
?>
<?php //edit_locales_print_drupal_locales_plurales();?>
<?php //edit_locales_print_podx_plurales();?>
<?php //edit_locales_print_insert_con_castellano();?>
<html>
	<head>
		<title>Translate Drupal locales</title>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	</head>
	<body>
<?php

$output = '';

$languages = array('eu', 'es', 'fr', 'pt-pt');


$count = 20; // zenbat esaldi orriko
$offset = isset($_GET['page']) ? $_GET['page'] * $count : 0; // paginaziorako hasierako erregistroa
$target = isset($_GET['target']) ? $_GET['target'] : ''; // zein hizkuntzatako esaldiak editatu nahi diren
$referers = array('es'); // zein hizkuntza erabili nahi diren erreferentzia moduan

if(empty($target)){
    $target='es';
}

$target_konp=$target;

if($target_konp=='pt-pt'){
   $target_konp='pt'; 
}

$conditions = array('1');
$my_filter='';
if(isset($_GET['filter'])){
	if($_GET['filter'] == 'translated'){
		$conditions[] = "locales_target_".$target_konp.".translation <> ''";
	}
	if($_GET['filter'] == 'untranslated'){
		$conditions[] = "(locales_target_".$target_konp.".translation = '' OR locales_target_".$target_konp.".translation IS NULL)";
	}
        
        //simulando
        //$conditions[]='s.source="@count guests accessing site; throttle disabled"';
        //$conditions[]='s.source=" Pass both groups in URL and select none - 1st group not found, as expected."';
        //$conditions[]='s.lid=4';
        
        $my_filter=$_GET['filter'];
}
$where = '(' . join($conditions, ') AND (') . ')';

$result = db_query('SELECT COUNT(*) zenbat 
        FROM locales_source s 
        LEFT JOIN locales_target locales_target_eu ON (s.lid = locales_target_eu.lid AND locales_target_eu.language="eu")
        LEFT JOIN locales_target locales_target_es ON (s.lid = locales_target_es.lid AND locales_target_es.language="es")
        LEFT JOIN locales_target locales_target_pt ON (s.lid = locales_target_pt.lid AND locales_target_pt.language="pt-pt")
        LEFT JOIN locales_target locales_target_fr ON (s.lid = locales_target_fr.lid AND locales_target_fr.language="fr")
        WHERE ' . $where);
if(!$result){
    print db_error();
    exit();
}    
$total = db_fetch_object($result);


$result = db_query($sql=sprintf(
        'SELECT s.source,
        s.lid,    
        locales_target_eu.translation AS eu,
        locales_target_es.translation AS es,
        locales_target_pt.translation AS pt,
        locales_target_fr.translation AS fr
        FROM locales_source s 
        LEFT JOIN locales_target locales_target_eu ON (s.lid = locales_target_eu.lid AND locales_target_eu.language="eu")
        LEFT JOIN locales_target locales_target_es ON (s.lid = locales_target_es.lid AND locales_target_es.language="es")
        LEFT JOIN locales_target locales_target_pt ON (s.lid = locales_target_pt.lid AND locales_target_pt.language="pt-pt")
        LEFT JOIN locales_target locales_target_fr ON (s.lid = locales_target_fr.lid AND locales_target_fr.language="fr")
	WHERE ' . $where . '
	LIMIT %d, %d', $offset, $count));

if(!$result){
    print db_error();
    exit();
}  


$rows = array();
while($r = db_fetch_object($result)){
	$row = array('<tr><td class="language-code">EN</td><td class="interface-text source">' . $r->source . '</td></tr>');
	foreach($referers as $language){
		$row[] = '<tr><td class="language-code">' . strtoupper($language) . '</td><td class="interface-text translation">' . $r->$language . '</td></tr>';
	}
	if(!empty($target)){
		$len = strlen($r->source);
		if($len < 54){
			$input = '<input size="60" type="text" name="translations['.$r->lid.']['.$target.']" value="' . $r->$target_konp . '" />';
		} else {
			$input = '<textarea name="translations['.$r->lid.']['.$target.']" cols="80" rows="'.(ceil($len/50)).'">' . $r->$target_konp . '</textarea>';
		}
		$row[] = '<tr><td class="language-code">' . strtoupper($target) . '</td><td class="interface-text edition">' . $input . '</td></tr>';
	}
	$rows[] = array($r->lid, '<table>' . join($row, "\n") . ' </table>');
}

$css = '<style>
.pagination a:active{
	text-decoration: none;
	color: black;
	font-weight: bold;
}
#translations table{
	margin: 0;
}
#translations td{
	vertical-align: top;
	max-width: 600px;
	padding: 4px;
}
#translations td.language-code{
	background-color: #eee;
	padding: 4px;
	text-align: right;
}
#translations td.lid{
	background-color: #ddd;
	padding:5px;
}
</style>';

$table = '<table id="translations">';
foreach($rows as $row){
	$table .= '<tr class="item"><td class="lid">' . join($row, '</td><td class="esaldi">') . '</td></tr>';
}
$table .= '</table>';

$pagination_links = array();
if($total->zenbat > $count){
	for($i = 0; ($i*$count) < $total->zenbat; $i++){
		$href = 'edit-locales.php?' . http_build_query(array_merge($_GET, array('page' => $i)));
		$pagination_links[] = '<a href="' . $href . '">' . $i . '</a>';
	}
}
if($pagination_links){
	$pagination = '<div class="pagination">' . t('Select page') . ': ' . join($pagination_links, ' &middot; ') . '</div>';
}

$filter_links = array();
$filter_options = array('translated' => 'Translated', 'untranslated' => t('Untranslated'), 'all' => t('None'));
foreach($filter_options as $key => $title){
        $my_array=array_merge($_GET, array('filter' => $key));
        if(isset($my_array['page'])){
            unset($my_array['page']);
        }
	$href = 'edit-locales.php?' . http_build_query($my_array);
	$filter_links[] = '<a href="' . $href . '">' . $title . '</a>';
}

$output .= $css;

if(!empty($languages)){
    foreach($languages as $k=>$code){
        $my_array=array_merge($_GET, array('filter' => $key));
        $href = 'edit-locales.php?' . http_build_query($my_array);
        if(isset($my_array['page'])){
            unset($my_array['page']);
        }
        if(isset($my_array['target'])){
            unset($my_array['target']);
        }
        $my_array['target']=$code;
	$title=$code;
        $href = 'edit-locales.php?' . http_build_query($my_array);
        $lang_links[] = '<a href="' . $href . '">' . $title . '</a>';
    }
}

$output .= '<div class="selected_language">' . t('Selected language') . ': <b>' .$target. '</b></div>';
$output .= '<div class="selected_filter">' . t('Selected filter') . ': <b>' .$my_filter. '</b></div>';
$output .= '<div class="languages">' . t('Languages') . ': ' . join($lang_links, ' &middot; ') . '</div>';
$output .= '<div class="filter">' . t('Filter items') . ': ' . join($filter_links, ' &middot; ') . '</div>';
$output .= $pagination;
$output .= '<form method="post">';
$output .= $table;
$output .= '<input type="submit" name="update_translations_btn" value="Update translations" /></form>';
// $output .= $pagination;

print $output;
/*
function t($source){
	return $source;
}*/

?>
	</body>
</html>
<?php
function edit_locales_save_translations($translations){
    if(!empty($translations)){
        foreach($translations as $lid=>$value_array){
            //$is_plural=edit_locales_is_plural_by_lid($lid);
            $keys=array_keys($value_array);
            $values=array_values($value_array);
            $lang=$keys[0];
            $value=$values[0];
            $target_row=edit_locales_get_target_row($lid,$lang);
            if(isset($target_row->lid)){
               db_query('UPDATE {locales_target} SET translation="%s" WHERE lid=%d AND language="%s"',$value,$lid,$lang);
            }else{
                $new_row=new stdClass();
                $new_row->lid=$lid;
                $new_row->translation=$value;
                $new_row->plid=0;
                $new_row->plural=0;
                $new_row->l10n_status=0;
                $new_row->i18n_status=0;
                $new_row->language=$lang;
                $copiar_row=edit_locales_get_target_row($lid,'es');
                //print $lid;exit();
                if(isset($copiar_row->lid) && !empty($copiar_row->lid)){
                    $new_row->plid=$copiar_row->plid;
                    $new_row->plural=$copiar_row->plural;
                    //$new_row->l10n_status=$copiar_row->l10n_status;
                    //$new_row->i18n_status=$copiar_row->i18n_status;                                 
                }
                //$sql=sprintf('INSERT INTO {locales_target}(lid,translation,language,plid,plural,l10n_status,i18n_status) VALUES(%d,"%s","%s",%d,%d,%d,%d)',$lid,$new_row->translation,$new_row->language,$new_row->plid,$new_row->plural,$new_row->l10n_status,$new_row->i18n_status);
                //print $sql;exit();
                db_query('INSERT INTO {locales_target}(lid,translation,language,plid,plural,l10n_status,i18n_status) VALUES(%d,"%s","%s",%d,%d,%d,%d)',$lid,$new_row->translation,$new_row->language,$new_row->plid,$new_row->plural,$new_row->l10n_status,$new_row->i18n_status);                    
            }    
        }
    }
}
function edit_locales_get_target_row($lid,$lang){
    $res=db_query('SELECT * FROM {locales_target} WHERE lid=%d AND language="%s"',$lid,$lang);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function edit_locales_get_source_row($lid){
    $res=db_query('SELECT * FROM {locales_source} WHERE lid=%d',$lid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    //
    $my_result=new stdClass();
    return $my_result;
}
function edit_locales_is_plural_by_drupal_locales($source_row){
    //simulando
    //$source_row->source='1 user accessing site; throttle enabled.';
    //$source_row->location='podx:'. $source_row->location;
    //
    $row=edit_locales_get_drupal_locales_source($source_row);
    if(isset($row->lid) && !empty($row->lid)){
        $plural=1;
        $res=db_query('SELECT * FROM sources WHERE lid=%d AND plural=%d',$row->lid,$plural);
        while($row=db_fetch_object($res)){
            return 1;
        }
    }
    return 0;
}
function edit_locales_get_drupal_locales_source($source_row){
    $location=$source_row->location;
    $pos=strpos($location,'podx:');
    if($pos===FALSE){
        $my_result=new stdClass();
        return $my_result;
    }
    return edit_locales_get_drupal_locales_source_row($source_row->source);
}
function edit_locales_get_drupal_locales_source_row($source){
    $res=db_query('SELECT * FROM sources WHERE source="%s"',$source);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function edit_locales_is_plural_by_lid($lid){
            $source_row=edit_locales_get_source_row($lid);
            db_set_active('drupal_locales');
            $is_plural=edit_locales_is_plural_by_drupal_locales($source_row);
            db_set_active();
            //print 'is_plural='.$is_plural;exit();            
            return $is_plural;
}
function edit_locales_print_drupal_locales_plurales(){
    $result=array();
    $sql='SELECT * 
    FROM sources
    WHERE plural=1';
    db_set_active('drupal_locales');    
    $res=db_query($sql);
    db_set_active();    
    while($row=db_fetch_object($res)){
        db_set_active('drupal_locales');    
        $source_row=edit_locales_get_drupal_locales_source_row_by_lid($row->lid);
        db_set_active();
        if(isset($source_row->source)){
            $result[]=$source_row->source;
        }    
    }
    echo print_r($result,1);exit();
    print implode('<BR>',$result);
    exit();
    return $result;
}
function edit_locales_get_drupal_locales_source_row_by_lid($lid){
    $res=db_query('SELECT * FROM sources WHERE lid=%d AND plural=0',$lid);
    while($row=db_fetch_object($res)){
        return $row;
    }
    $my_result=new stdClass();
    return $my_result;
}
function edit_locales_print_podx_plurales(){
    $podx_array=edit_locales_get_podx_array();
    if(!empty($podx_array)){
        foreach($podx_array as $i=>$row){
            //$is_plural=edit_locales_is_plural_by_drupal_locales($row);
            //if($is_plural){
                print $row->source.'===='.$is_plural.'<BR>';
            //}
        }
    }
    print date('Y-m-d H:i');
    exit();
}
function edit_locales_get_podx_array(){
    $result=array();
    $res=db_query('SELECT * FROM locales_source WHERE location LIKE "%podx:%"');
    while($row=db_fetch_object($res)){
        $result[]=$row;
    }
    return $result;
}
function edit_locales_print_insert_con_castellano(){
    $result=array();
    $res=db_query('SELECT * FROM locales_source WHERE 1');
    while($row=db_fetch_object($res)){
        $copiar_row=edit_locales_get_target_row($row->lid,'pt-pt');
        if(isset($copiar_row->lid)){
            //echo print_r($copiar_row,1);
            //exit();
        }else{
            $copiar_es=edit_locales_get_target_row($row->lid,'es');
             if(isset($copiar_es->lid)){
                echo print_r($copiar_es,1);
                exit();
             }
        }
    }
    return $result;
}
?>