<?php

require_model('agente.php');

class admin_agentes extends fs_controller
{
   public $agente;
   
   public function __construct()
   {
      parent::__construct(__CLASS__, 'Empleados', 'admin', TRUE, TRUE);
   }
   
   protected function private_core()
   {
      $this->agente = new agente();
      
      if( isset($_POST['sdnicif']) )
      {
         $age0 = new agente();
         $age0->codagente = $age0->get_new_codigo();
         $age0->nombre = $_POST['snombre'];
         $age0->apellidos = $_POST['sapellidos'];
         $age0->dnicif = $_POST['sdnicif'];
         $age0->telefono = $_POST['stelefono'];
         $age0->email = $_POST['semail'];
         if( $age0->save() )
         {
            $this->new_message("Empleado ".$age0->codagente." guardado correctamente.");
            header('location: '.$age0->url());
         }
         else
            $this->new_error_msg("¡Imposible guardar el empleado!");
      }
      else if( isset($_GET['delete']) )
      {
         $age0 = $this->agente->get($_GET['delete']);
         if($age0)
         {
            if( FS_DEMO )
            {
               $this->new_error_msg('En el modo <b>demo</b> no se pueden eliminar empleados. Otro usuario podría estar usándolo.');
            }
            else if( $age0->delete() )
            {
               $this->new_message("Empleado ".$age0->codagente." eliminado correctamente.");
            }
            else
               $this->new_error_msg("¡Imposible eliminar el empleado!");
         }
         else
            $this->new_error_msg("¡Empleado no encontrado!");
      }
   }
}
