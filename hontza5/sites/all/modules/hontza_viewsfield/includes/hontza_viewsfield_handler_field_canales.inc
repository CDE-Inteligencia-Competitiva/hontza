<?php
// $Id: hontza_viewsfield,v 1.1.4.2 2010/31/09 18:32:33 investic Exp $
/**
 * @file
 * Contains the 'customfield' markup field handler.
 */

/**
 * Field handler to display custom markup text.
 */
class hontza_viewsfield_handler_field_canales extends views_handler_field {
  function init(&$view, $options) {
    parent::init($view, $options);
  }
  
  function query() {
    $this->field_alias = 'customfield_rownumber_'. $this->position;
  }

  function render($values) {
    $tit=NULL;
    $nids_canales = db_query("SELECT c.nid FROM {node} n LEFT OUTER JOIN {content_field_nombrefuente_canal} c ON n.title = c.field_nombrefuente_canal_value WHERE (n.type = 'fuentedapper' OR n.type = 'supercanal' )  AND n.nid = %d", $values->nid);
    while ($nodo_nid = db_result($nids_canales)) {
      $nodo=node_load($nodo_nid);
      $tit .='<A HREF="/node/' . $nodo->nid .'" TARGET="_blank"><li>' . $nodo->title . '</li></A>';
    }
    return $tit;
  }
}
