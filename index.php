<?php

/// Si estas leyendo esto es porque no tienes PHP instalado !!!!!!!!!!!!!!!!!!!!

if( !file_exists('config.php') )
{
   /// si no hay config.php redirigimos al instalador
   header('Location: install.php');
}
else
{
   /// cargamos las constantes de configuración
   require_once 'config.php';
   require_once 'base/config2.php';
   
   require_once 'base/fs_controller.php';
   require_once 'raintpl/rain.tpl.class.php';
   
   /// ¿Qué controlador usar?
   $pagename = '';
   if( isset($_GET['page']) )
   {
      $pagename = $_GET['page'];
   }
   else if( defined('FS_HOMEPAGE') )
   {
      $pagename = FS_HOMEPAGE;
   }
   
   if($pagename != '')
   {
      /// primero buscamos en los plugins
      $found = FALSE;
      foreach($GLOBALS['plugins'] as $plugin)
      {
         if( file_exists('plugins/'.$plugin.'/controller/'.$pagename.'.php') )
         {
            require_once 'plugins/'.$plugin.'/controller/'.$pagename.'.php';
            $fsc = new $pagename();
            $found = TRUE;
            break;
         }
      }
      
      /// si no está en los plugins, buscamos en controller/
      if( !$found )
      {
         if( file_exists('controller/'.$pagename.'.php') )
         {
            require_once 'controller/'.$pagename.'.php';
            
            try
            {
               $fsc = new $pagename();
            }
            catch(Exception $e)
            {
               echo "<h1>Error fatal</h1>";
               echo "Mensage: " . $e->getMessage();
               echo "Código: " . $e->getCode();
            }
         }
         else
         {
            header("HTTP/1.0 404 Not Found");
            $fsc = new fs_controller();
         }
      }
   }
   else
   {
      $fsc = new fs_controller();
   }
   
   if( !isset($_GET['page']) )
   {
      /// redireccionamos a la página definida por el usuario
      $fsc->select_default_page();
   }
   
   if($fsc->template)
   {
      /// configuramos rain.tpl
      raintpl::configure('base_url', NULL);
      raintpl::configure('tpl_dir', 'view/');
      raintpl::configure('path_replace', FALSE);
      
      /// ¿Se puede escribir sobre la carpeta temporal?
      if( is_writable('tmp') )
      {
         raintpl::configure('cache_dir', 'tmp/');
      }
      else
      {
         echo '<center>'
         . '<h1>No se puede escribir sobre la carpeta tmp de ECON</h1></center>';
      }
      
      $tpl = new RainTPL();
      $tpl->assign('fsc', $fsc);
      
      if( isset($_POST['user']) )
      {
         $tpl->assign('nlogin', $_POST['user']);
      }
      else if( isset($_COOKIE['user']) )
      {
         $tpl->assign('nlogin', $_COOKIE['user']);
      }
      else
         $tpl->assign('nlogin', '');
      
      $tpl->draw( $fsc->template );
   }
   
   $fsc->close();
}
