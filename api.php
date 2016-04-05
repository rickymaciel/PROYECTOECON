<?php

/// cargamos las constantes de configuración
require_once 'config.php';
require_once 'base/config2.php';

require_once 'base/fs_db2.php';
$db = new fs_db2();

require_once 'base/fs_model.php';
require_model('fs_extension.php');

if( $db->connect() )
{
   if( !isset($_REQUEST['v']) )
   {
      echo 'Version de la API ausente. Actualiza el cliente.';
   }
   else if($_REQUEST['v'] == '2')
   {
      if( isset($_REQUEST['f']) )
      {
         $ejecutada = FALSE;
         $fsext = new fs_extension();
         foreach($fsext->all_4_type('api') as $ext)
         {
            if($ext->text == $_REQUEST['f'])
            {
               try
               {
                  $_REQUEST['f']();
               }
               catch(Exception $e)
               {
                  echo 'ERROR: '.$e->getMessage();
               }
               
               $ejecutada = TRUE;
            }
         }
         
         if(!$ejecutada)
         {
            echo 'Ninguna funcion API ejecutada.';
         }
      }
      else
         echo 'Ninguna funcion ejecutada.';
   }
   else
   {
      echo 'Version de la API incorrecta. Actualiza el cliente.';
   }
}
else
   echo 'ERROR al conectar a la base de datos';
