<?php

abstract class HelperPDO {
    protected $pdo;
    protected $table;
    protected $columns = array();
    protected $primaryKey;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function query($where = array())
    {
        $sql = "SELECT * FROM {$this->table}";
        if(!empty($where)) {
            $sql .= " WHERE ";
            foreach($where as $field=>$value) {
                $sql .= $field . " = " . $value;
            }
        }
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByPrimaryKey($value)
    {
        return $this->query(array($this->primaryKey => $value));
    }

    public function insert($fields, $values)
    {
        $sql = "INSERT INTO {$this->table} (".implode(',', $fields).") VALUES (".implode(',', $values).")";
        echo $sql;
        return $this->pdo->exec($sql);
    }

    public function update()
    {

    }

    public function delete()
    {

    }
}