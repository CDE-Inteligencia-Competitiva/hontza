</div>
    </div>
 <?php if(hontza_is_footer()):?>
  <div id="footer" class="layout-region" style="width:960px;margin: 0 auto -1em;">
    <?php print hontza_canal_rss_get_logos_apis();?>
    <?php //intelsat-2015?>
    <!--  
    <div id="footer-inner">
    -->
    <div id="footer-inner" class="footer-inner-integrated-services" style="padding-top:20px;">
      <?php print $contentfooter; ?>
      <?php //gemini ?>
      <?php //print $footer_message; ?>
      <?php print get_frase_powered('castellano');?>
        <BR>
      <?php print get_frase_powered('ingles');?>
    </div>
  </div>
  <?php else:?>  
  <div id="footer" class="layout-region" style="width:960px;margin: 0 auto -1em;">
    <?php //print hontza_canal_rss_get_logos_apis();?>  
    <div id="footer-inner-light">
      <?php print $contentfooter; ?>
      <?php //gemini ?>
      <?php //print $footer_message; ?>
      <?php print hontza_get_frase_powered_light();?>       
    </div>
  </div>  
  <?php endif;?>  
  <?php print $closure; ?>
  </body>
</html>