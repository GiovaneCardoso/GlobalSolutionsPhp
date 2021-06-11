<?php


namespace app\database;

use Exception;

abstract class Model
{

    public $table_info;

    public $timestamp = true;

    public $table_name;

    protected $connection;

    private $db_conf;

    /**
     * carDAO constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->db_conf = new Connection();

        $this->connection = $this->db_conf->getConnection();

        $this->setTableInfo();
    }

    private function binTimeStamp( array $attr, $create=true, $update=true )
    {
        if($this->timestamp === false) return $attr;

        if($create)
            $attr['created_at'] = "'".date('Y-m-d H:i:s')."'";

        if($update)
            $attr['updated_at'] = "'".date('Y-m-d H:i:s')."'";

        return $attr;
    }

    protected function runQuery( $sql )
    {
        $result = $this->connection->query($sql);

        $list = [];
        while($record = $result->fetch_object())
            $list[] = $record;

        return $list;
    }

    /**
     * get table configs
     */
    public function setTableInfo()
    {
        $table_info = $this->runQuery(
            "SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS  
                WHERE table_name = '{$this->table_name}' AND TABLE_SCHEMA = '{$this->db_conf->database}'");

        $this->table_info = $table_info;
    }

    private function prepareAttributes( array $attrs )
    {

        foreach ($this->table_info as $column) {

            if(isset($attrs[$column->COLUMN_NAME]))
            {
                switch ($column->DATA_TYPE)
                {
                    case 'varchar':
                        $attrs[$column->COLUMN_NAME] = "'".$attrs[$column->COLUMN_NAME]."'";
                        break;
                }
            }
        }

        return $attrs;
    }


    /**
     * close db database
     */
    public function __destruct()
    {
        $this->closeConnection();
    }

    /**
     * @return bool
     */
    public function closeConnection()
    {
        return $this->connection->close();
    }

    /**
     * insert line at database
     *
     * @param $columns
     * @param $values
     * @return bool
     * @throws Exception
     */
    private function insertLine( $columns, $values )
    {
        $sql = "INSERT INTO {$this->table_name} ( $columns ) VALUES ( {$values} )";

        if(!$this->connection->query($sql)) throw new Exception($this->connection->error);

        return $this->connection->insert_id;
    }
    /**
     * insert line at database
     *
     * @param $columns
     * @param $values
     * @return bool
     * @throws Exception
     */
    private function updateLine( $attrs, $id )
    {
        $sets = [];

        foreach ($attrs as $column => $attr)
            $sets[] = "$column = $attr";

        $sets = implode(', ', $sets);

        $sql = "UPDATE {$this->table_name} SET $sets where id = $id";

        if(!$this->connection->query($sql)) throw new Exception($this->connection->error);

        return $id;
    }

    /**
     * @param array $attrs
     * @return array|null
     * @throws Exception
     */
    public function create( array $attrs ){

        $attrs = $this->prepareAttributes($this->binTimeStamp($attrs));

        $values = implode(',', $attrs );

        $id = $this->insertLine( implode(',', array_keys($attrs)), $values);

        return $this->find($id);
    }

    /**
     * @return array
     */
    public function index(){
        return $this->runQuery("SELECT * FROM {$this->table_name}");
    }

    /**
     * @param $id
     * @return array
     */
    public function find($id){
        return $this->getFirst("SELECT * FROM {$this->table_name} WHERE id=$id");
    }


    public function addWheres( array $attrs )
    {
        $wheres = [];
        $first = true;

        foreach ($attrs as $column => $value ) {
            $add = 'and';
            if($first) {
                $add = 'where'; $first=false;
            }
            $wheres[] = "{$add} {$column} = {$value}";
        }

        return implode(' ', $wheres);
    }


    private function getFirst( $sql )
    {
        $result = $this->connection->query($sql);

        if(!$result) return null;

        $list = [];
        while($record = $result->fetch_object())
            $list[] = $record;

        return isset($list[0]) ? $list[0]:null;
    }

    /**
     * @param $id
     * @return array
     */
    public function findBy(array $attrs){

        $where = $this->addWheres($this->prepareAttributes($attrs));

        return $this->getFirst("SELECT * FROM {$this->table_name} {$where}");
    }


    /**
     * @param $id
     * @return array
     */
    public function selectBy(array $attrs){

        $where = $this->addWheres($this->prepareAttributes($attrs));

        return $this->runQuery("SELECT * FROM {$this->table_name} {$where}");
    }

    /**
     * @param $id
     * @param $attrs
     * @return array|null
     * @throws Exception
     */
    public function update($id, $attrs){

        $attrs = $this->prepareAttributes($this->binTimeStamp($attrs, false));

        $this->updateLine( $attrs, $id );

        return $this->find($id);

    }

    /**
     * @param $id
     * @return bool|mysqli_result
     */
    public function remove($id)
    {
        return $this->connection->query("DELETE FROM {$this->table_name} WHERE id=$id");
    }


}