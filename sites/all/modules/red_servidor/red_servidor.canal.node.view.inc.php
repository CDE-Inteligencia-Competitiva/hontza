<?php
function red_servidor_canal_node_view($node){ 
  $node = (object)$node;

  $node = node_build_content($node, $teaser, $page);
  
  $page=1;
  
  if ($links) {
    $node->links = module_invoke_all('link', 'node', $node, $teaser);
    drupal_alter('link', $node->links, $node);
  }
  
  // Set the proper node part, then unset unused $node part so that a bad
  // theme can not open a security hole.
  
  $content = drupal_render($node->content);
  if ($teaser) {
    $node->teaser = $content;
    unset($node->body);
  }
  else {
    $node->body = $content;
    unset($node->teaser);
  }
  
  // Allow modules to modify the fully-built node.
  node_invoke_nodeapi($node, 'alter', $teaser, $page);
  
  return theme('node', $node, $teaser, $page);
}