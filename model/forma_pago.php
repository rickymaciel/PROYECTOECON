<?php


/**
 * Forma de pago de una factura.
 */
class forma_pago extends fs_model
{
   /**
    * Clave primaria. Varchar (10).
    * @var type 
    */
   public $codpago;
   public $descripcion;
   
   /**
    * Pagados -> marca las facturas generadas como pagadas.
    * @var type 
    */
   public $genrecibos;
   
   /**
    * Código de la cuenta bancaria asociada.
    * @var type 
    */
   public $codcuenta;
   
   /**
    * Para indicar si hay que mostrar la cuenta bancaria del cliente.
    * @var type 
    */
   public $domiciliado;
   
   /**
    * Sirve para generar la fecha de vencimiento de las facturas.
    * @var type 
    */
   public $vencimiento;
   
   public function __construct($f=FALSE)
   {
      parent::__construct('formaspago');
      if( $f )
      {
         $this->codpago = $f['codpago'];
         $this->descripcion = $f['descripcion'];
         $this->genrecibos = $f['genrecibos'];
         $this->codcuenta = $f['codcuenta'];
         $this->domiciliado = $this->str2bool($f['domiciliado']);
         $this->vencimiento = $f['vencimiento'];
      }
      else
      {
         $this->codpago = NULL;
         $this->descripcion = '';
         $this->genrecibos = 'Emitidos';
         $this->codcuenta = '';
         $this->domiciliado = FALSE;
         $this->vencimiento = '+1month';
      }
   }
   
   public function install()
   {
      $this->clean_cache();
      return "INSERT INTO ".$this->table_name." (codpago,descripcion,genrecibos,codcuenta,domiciliado,vencimiento)"
              . " VALUES ('CONT','Al contado','Pagados',NULL,FALSE,'+1month')"
              . ",('TRANS','Transferencia bancaria','Emitidos',NULL,FALSE,'+1month')"
              . ",('PAYPAL','PayPal','Pagados',NULL,FALSE,'+1week');";
   }
   
   public function url()
   {
      return 'index.php?page=contabilidad_formas_pago';
   }
   
   public function is_default()
   {
      return ( $this->codpago == $this->default_items->codpago() );
   }
   
   public function get($cod)
   {
      $pago = $this->db->select("SELECT * FROM ".$this->table_name." WHERE codpago = ".$this->var2str($cod).";");
      if($pago)
      {
         return new forma_pago($pago[0]);
      }
      else
         return FALSE;
   }

   public function exists()
   {
      if( is_null($this->codpago) )
      {
         return FALSE;
      }
      else
         return $this->db->select("SELECT * FROM ".$this->table_name." WHERE codpago = ".$this->var2str($this->codpago).";");
   }
   
   public function save()
   {
      $this->descripcion = $this->no_html($this->descripcion);
      $this->clean_cache();
      
      if( $this->exists() )
      {
         $sql = "UPDATE ".$this->table_name." SET descripcion = ".$this->var2str($this->descripcion).
                 ", genrecibos = ".$this->var2str($this->genrecibos).
                 ", codcuenta = ".$this->var2str($this->codcuenta).
                 ", domiciliado = ".$this->var2str($this->domiciliado).
                 ", vencimiento = ".$this->var2str($this->vencimiento).
                 " WHERE codpago = ".$this->var2str($this->codpago).";";
      }
      else
      {
         $sql = "INSERT INTO ".$this->table_name." (codpago,descripcion,genrecibos,codcuenta,domiciliado,vencimiento)
                 VALUES (".$this->var2str($this->codpago).
                 ",".$this->var2str($this->descripcion).
                 ",".$this->var2str($this->genrecibos).
                 ",".$this->var2str($this->codcuenta).
                 ",".$this->var2str($this->domiciliado).
                 ",".$this->var2str($this->vencimiento).");";
      }
      
      return $this->db->exec($sql);
   }
   
   public function delete()
   {
      $this->clean_cache();
      return $this->db->exec("DELETE FROM ".$this->table_name." WHERE codpago = ".$this->var2str($this->codpago).";");
   }
   
   private function clean_cache()
   {
      $this->cache->delete('m_forma_pago_all');
   }
   
   public function all()
   {
      $listaformas = $this->cache->get_array('m_forma_pago_all');
      if( !$listaformas )
      {
         $formas = $this->db->select("SELECT * FROM ".$this->table_name." ORDER BY descripcion ASC;");
         if($formas)
         {
            foreach($formas as $f)
               $listaformas[] = new forma_pago($f);
         }
         $this->cache->set('m_forma_pago_all', $listaformas);
      }
      return $listaformas;
   }
}
