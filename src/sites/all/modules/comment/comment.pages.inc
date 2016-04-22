<?php

/**
 * @file
 * User page callbacks for the comment module.
 */

/**
 * Form builder; generate a comment editing form.
 *
 * @param $cid
 *   ID of the comment to be edited.
 * @ingroup forms
 */
function comment_edit($cid) {
  global $user;

  $comment = db_fetch_object(db_query('SELECT c.*, u.uid, u.name AS registered_name, u.data FROM {comments} c INNER JOIN {users} u ON c.uid = u.uid WHERE c.cid = %d', $cid));
  $comment = drupal_unpack($comment);
  $comment->name = $comment->uid ? $comment->registered_name : $comment->name;
  if (comment_access('edit', $comment)) {
    return comment_form_box((array)$comment);
  }
  else {
    drupal_access_denied();
  }
}

/**
 * This function is responsible for generating a comment reply form.
 * There are several cases that have to be handled, including:
 *   - replies to comments
 *   - replies to nodes
 *   - attempts to reply to nodes that can no longer accept comments
 *   - respecting access permissions ('access comments', 'post comments', etc.)
 *
 * The node or comment that is being replied to must appear above the comment
 * form to provide the user context while authoring the comment.
 *
 * @param $node
 *   Every comment belongs to a node. This is that node.
 *
 * @param $pid
 *   Some comments are replies to other comments. In those cases, $pid is the parent
 *   comment's cid.
 *
 * @return
 *   The rendered parent node or comment plus the new comment form.
 */
function comment_reply($node, $pid = NULL) {

  // Set the breadcrumb trail.
  drupal_set_breadcrumb(array(l(t('Home'), NULL), l($node->title, 'node/'. $node->nid)));
  $op = isset($_POST['op']) ? $_POST['op'] : '';

  $output = '';
  
  //gemini-2014
  $div_opciones='';
  //  

  $is_publico=0;
   if(hontza_canal_rss_is_publico_activado()){
        if(publico_is_pantalla_publico('vigilancia')){
            $is_publico=1;
        }
   }
    
  if (user_access('access comments') || $is_publico) {
    // The user is previewing a comment prior to submitting it.
    if ($op == t('Preview')) {
      if (user_access('post comments') || $is_publico) {
        $output .= comment_form_box(array('pid' => $pid, 'nid' => $node->nid), NULL);
      }
      else {
        drupal_set_message(t('You are not authorized to post comments.'), 'error');
        drupal_goto("node/$node->nid");
      }
    }
    else {
      // $pid indicates that this is a reply to a comment.
      if ($pid) {
        // load the comment whose cid = $pid
        if ($comment = db_fetch_object(db_query('SELECT c.*, u.uid, u.name AS registered_name, u.signature, u.signature_format, u.picture, u.data FROM {comments} c INNER JOIN {users} u ON c.uid = u.uid WHERE c.cid = %d AND c.status = %d', $pid, COMMENT_PUBLISHED))) {
          // If that comment exists, make sure that the current comment and the parent comment both
          // belong to the same parent node.
          if ($comment->nid != $node->nid) {
            // Attempting to reply to a comment not belonging to the current nid.
            drupal_set_message(t('The comment you are replying to does not exist.'), 'error');
            drupal_goto("node/$node->nid");
          }
          // Display the parent comment
          $comment = drupal_unpack($comment);
          $comment->name = $comment->uid ? $comment->registered_name : $comment->name;
          $output .= theme('comment_view', $comment, $node);
        }
        else {
          drupal_set_message(t('The comment you are replying to does not exist.'), 'error');
          drupal_goto("node/$node->nid");
        }
      }
      // This is the case where the comment is in response to a node. Display the node.
      else if (user_access('access content')) {
        //gemini-2013  
        //$output .= node_view($node);
        if(hontza_is_show_node_titulo($node)){
            $output .=hontza_node_titulo_html($node);
        }
        //gemini-2014
        $output .= hontza_set_display_none_opciones_item(node_view($node,FALSE,1),$div_opciones);
        //  
      }

      // Should we show the reply box?
      //intelsat-2015
      if (node_comment_mode($node->nid) != COMMENT_NODE_READ_WRITE && !$is_publico) {
        drupal_set_message(t("This discussion is closed: you can't post new comments."), 'error');
        drupal_goto("node/$node->nid");
      }
      else if (user_access('post comments') || $is_publico) {
        //gemini-2013  
        //$output .= comment_form_box(array('pid' => $pid, 'nid' => $node->nid), t('Reply'));
        //gemini-2014
        //$output .= comment_form_box(array('pid' => $pid, 'nid' => $node->nid),'');
        $output .= comment_form_box(array('pid' => $pid, 'nid' => $node->nid,'div_opciones'=>$div_opciones),'');        
        //$output .=$div_opciones;
        //
      }
      else {
        drupal_set_message(t('You are not authorized to post comments.'), 'error');
        drupal_goto("node/$node->nid");
      }
    }
  }
  else {
    drupal_set_message(t('You are not authorized to view comments.'), 'error');
    drupal_goto("node/$node->nid");
  }

  return $output;
}
