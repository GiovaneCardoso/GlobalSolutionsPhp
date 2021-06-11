<?php

namespace app\models;

use app\database\Model;

class Dealership extends Model
{

    public $table_name = 'dealerships';


    public function search( $value )
    {
        return $this->runQuery("select * from dealerships where name like '%{$value}%'");
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function deleteWitPrices($id)
    {
         $d = $this->connection->query("delete from dealerships where id = $id");
         $p = $this->connection->query("delete from dealership_prices where dealership_id = $id");

         if(!$d || !$p)  throw new \Exception($this->connection->error);

         return true;
    }

}