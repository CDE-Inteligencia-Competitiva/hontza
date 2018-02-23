<?php $path_custom=red_crear_usuario_get_path_custom_css();?>
<?php global $base_path;?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Vigilancia Tecnológica</title>
    
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
    
    <link href="<?php print $path_custom;?>css/bootstrap.min.css" rel="stylesheet" type='text/css'>
    <link href="<?php print $path_custom;?>css/main.css" rel="stylesheet" type='text/css'>
    <script type="text/javascript" src="<?php print $base_path;?>misc/jquery.js?s"></script>

  </head>
  <body>

    <section class="main-header">
      <div class="container">
        <div class="pull-left">
          <?php $logo=alerta_get_introduccion_logo_by_subdominio(0,'');?>  
          <img src="<?php print $logo;?>"/>
        </div>  
        <?php if(hontza_is_user_anonimo()):?>  
        <div class="pull-right div_menu_drupal">
          <?php print l(t('Request new password'),'user/password');?>
        </div>  
        <div class="pull-right div_menu_drupal">
          <?php print l(t('Log in'),'user');?>
        </div>  
        <div class="pull-right div_menu_drupal">
          <?php print l(t('Request new account'),'user/register');?>
        </div>
        <?php else:?>  
        <div class="pull-right div_menu_drupal">
          <?php print l(t('Home'),'node');?>
        </div>   
        <?php endif;?>  
      </div>
    </section>

      
    <?php if ($messages != ""): ?>
        <?php print $messages; ?>
    <?php endif; ?>  
      
    <section class="main-content">
      <div class="container">
        <?php if(red_crear_usuario_is_imagen_red_todo()):?>
          <div class="div_imagen_red_todo">
          <?php print red_crear_usuario_get_custom_css_hontza_imagen_red($path_custom,1);?>
          </div>    
        <?php else:?>
         <article class="col-md-6 left-section col_imagen_red">
            <?php print red_crear_usuario_get_custom_css_hontza_imagen_red($path_custom);?>                            
          <!--
          <h1><span>BIENVENIDO</span> A RED HONTZA, ES HORA DE VIGILAR EN RED.</h1>
          -->
        </article>
        <article class="col-md-6 right-section">
          <div class="div_article_right_section">  
            <h2><?php print $title;?></h2>  
          <?php print $content;?>
          </div> 
        </article>
        <?php endif;?>  
      </div>      
    </section>
     
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    
    <script src="<?php print $path_custom;?>js/bootstrap.min.js"></script>

    <?php if(hontza_is_footer()):?>
  <div id="footer" class="layout-region" style="width:100%;margin: 0 auto -1em;">
    <?php print hontza_canal_rss_get_logos_apis();?>      
    <?php //intelsat-2015?>
    <!--  
    <div id="footer-inner">
    -->
    <!--
    <div id="footer-inner" class="footer-inner-integrated-services" style="padding-top:20px;">  
      <?php //print $contentfooter; ?>
      <?php //gemini ?>
      <?php //print $footer_message; ?>
      <?php //print 'Powered by Hontza 3.0.';?>
      <?php //print get_frase_powered('castellano');?>
        <BR>
      <?php //print get_frase_powered('ingles');?>
    </div>
    -->
  </div>
  <?php else:?>

  <div id="footer" class="layout-region" style="width:100%;margin: 0 auto -1em;background-color:#424242;text-align:center;">
    <?php //print hontza_canal_rss_get_logos_apis();?>  
    <div id="footer-inner-light">
      <?php print $contentfooter; ?>
      <?php //gemini ?>
      <?php //print $footer_message; ?>
      <?php //intelsat-2015?>
      <?php //se ha comentado?>  
      <?php print hontza_get_frase_powered_light();?>       
    </div>
  </div>
  <?php endif;?>      
            
        </div>
    </body>
</html>

    
    
    
  </body>
</html>
