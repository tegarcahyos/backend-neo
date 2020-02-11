<?php
  require __DIR__ . "/../adodb/adodb.inc.php";

class TransformerConnect
{
  public $db_transformer;

  public function transformer_connect()
    {

      $db_host = '10.62.161.10';
      $db_username = 'pmo';
      $db_password = 'pass4pmo';
      $db_name_transformer = 'neotransformer';

        $this->db_transformer = newADOConnection('pgsql');
        $this->db_transformer->connect($db_host, $db_username, $db_password, $db_name_transformer);
        return $this->db_transformer;
    }
}