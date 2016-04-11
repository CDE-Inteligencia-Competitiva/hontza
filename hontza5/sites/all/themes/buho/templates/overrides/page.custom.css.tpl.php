<?php $path_custom=red_crear_usuario_get_path_custom_css();?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Red Alerta | Vigilancia Tecnológica</title>

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>

    <link href="<?php print $path_custom;?>css/bootstrap.min.css" rel="stylesheet" type='text/css'>
    <link href="<?php print $path_custom;?>css/main.css" rel="stylesheet" type='text/css'>

  </head>
  <body>

    <section class="main-header">
      <div class="container">
        <div class="pull-left">
          <img src="<?php print $path_custom;?>img/logo.png" />
        </div>  
        <div class="pull-right">
          <a href="#">¿Necesita ayuda?</a>
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
        <article class="col-md-6 left-section">
          <img src="<?php print $path_custom;?>img/ico-alerta.png" />
          <h1><span>BIENVENIDO</span> A RED ALERTA, ES HORA DE VIGILAR EN RED.</h1>
        </article>
        <article class="col-md-6 right-section">
            <h2><?php print $title;?></h2>  
          <?php print $content;?>
        </article>
      </div>
      <?php print red_funciones_alerta_financiado_por_html(0,1);?>        
    </section>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="<?php print $path_custom;?>js/bootstrap.min.js"></script>
  </body>
</html>