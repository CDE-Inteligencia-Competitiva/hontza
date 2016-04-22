<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?> node_movil_item">
<?php $my_user_info=my_get_user_info($node);?>
<?php if($page==0):?>
    <?php include('node-noticia-view.tpl.php');?>
<?php else:?>
    <?php include('node-noticia-complete-view.tpl.php');?>
<?php endif; ?>
</div>