<?php

abstract class AbstractDAO {
    protected $pdo;
    protected $table;
    protected $columns = array();
    protected $columns_not_null = array();
    protected $primaryKey;

    protected $messages = array(
        'insert_ok' => 'Dados inseridos com sucesso.',
        'insert_error' => 'Ocorreu um erro durante inserção dos dados.',
        'update_ok' => 'Dados atualizados com sucesso.',
        'update_empty' => 'A transação ocorreu corretamente, porém nenhum dado foi atualizado.',
        'update_error' => 'Ocorreu um erro durante atualização dos dados.',
        'delete_ok' => 'Dados excluídos com sucesso.',
        'delete_error' => 'Ocorreu um erro durante a exclusão dos dados.',
        'delete_no_param' => 'Necessário informar pelo menos um parâmetro para exclusão.',
        'exception' => 'Ocorreu um erro de banco de dados durante a transação.',
        'select_empty' => 'Nenhum dado encontrado com os parâmetros informados.',
        'select_ok' => 'Busca efetuada com sucesso.'
    );

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function destroyPdo() {
        $this->pdo = null;
        Database::destroyInstance();
    }

    public function setMessages($messages) {
        $this->messages = $messages;
        return $this->messages;
    }

    public function setMessage($name, $value) {
        $this->messages[$name] = $value;
    }

    public function query($params = array())
    {
        $sql = "SELECT * FROM {$this->table}";

        if(!empty($params)) {
            $sql .= " WHERE ";
            $fields = array_keys($params);
            foreach($fields as $k=>$field) {
                $sql .= $field . " = ? ";
                if($k < count($params)-1) {
                    $sql .= ' AND ';
                }
            }
        }

        $stmt = $this->pdo->prepare($sql);

        try {

            $query = $stmt->execute(array_values($params));
            $result = $stmt->fetchAll();

//            $stmt->debugDumpParams();exit;
            if (count($result)) {
                return array('status' => true, 'message' => $this->messages['select_ok'], 'result' => $result);
            } else {
                return array('status' => false, 'message' => $this->messages['select_empty']);
            }
        } catch (PDOException $e) {
            return array(
                'status'=>false,
                'message'=>$this->messages['exception'],
                'exception'=>$e->getMessage()
            );
        }
    }

    public function rawQuery($sql, $values) {
        $stmt = $this->pdo->prepare($sql);
        try{
            $stmt->execute($values);
            $result = $stmt->fetchAll();
            if (count($result)) {
                return array('status' => true, 'message' => $this->messages['select_ok'], 'result' => $result);
            } else {
                return array('status' => false, 'message' => $this->messages['select_empty']);
            }
        }catch (PDOException $e) {
            return array(
                'status'=>false,
                'message'=>$this->messages['exception'],
                'exception'=>$e->getMessage()
            );
        }
    }

    public function getByPrimaryKey($value)
    {
        $result = $this->query(array($this->primaryKey => $value));
        if(!empty($result)) {
            if($result['status']) {
                $result['result'] = $result['result'][0];
                return $result;
            }
        }
        return $result;
    }

    public function insert($params)
    {
        $params = $this->removeInvalidFields($params);
        if(!$this->checkNotNullColumns($params)) {
            return array(
                'status' => false,
                'message' => "As colunas a seguir são obrigatórias: ".implode(', ', $this->columns_not_null)
            );
        }
        $fields = array_keys($params);
        $values = array_values($params);

        $sql = "INSERT INTO {$this->table} (".implode(',', $fields).")";

        $sql .= " VALUES (".implode(',', array_fill(0, count($values), '?')) . ") ";

        $stmt = $this->pdo->prepare($sql);
        try{
            $stmt->execute($values);
            $id = $this->pdo->lastInsertId();
            if($id) {
                $result = $this->getByPrimaryKey($id);
                return array('status' => true, 'message' => $this->messages['insert_ok'], 'result' => $result);
            } else {
                return array('status' => false, 'message' => $this->messages['insert_error']);
            }
        } catch (PDOException $e) {
            return array(
                'status' => false,
                'message' => $this->messages['exception'],
//                'exception' => $e->getMessage()
            );
        }

    }

    public function update($params, $params_where)
    {
        $params = $this->removeInvalidFields($params);

        $fields = array_keys($params);
        $values = array_values($params);

        $sql = "UPDATE {$this->table} SET ";
        foreach($fields as $k=>$field) {
            $sql .= $field . " = ? ";
            if($k < count($fields)-1) {
                $sql .= ', ';
            }
        }
        $fields_where = array_keys($params_where);
        $values_where = array_values($params_where);
        if(count($fields_where)) {
            $sql .= " WHERE ";
            foreach($fields_where as $k=>$param) {
                $sql .= $param . " = ? ";
                if($k < count($fields_where)-1) {
                    $sql .= ' AND ';
                }
            }
        }

        $params = array_merge($values, $values_where);
        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute($params);
            if($stmt->rowCount()) {
                //verifica se foi feita uma exclusao por update
                if(array_key_exists('excluido', $params)) {
                    if ($params['excluido'] == 's') {
                       return array('status' => true, 'message' => $this->messages['delete_ok']);
                    }
                }
                return array('status' => true, 'message'=>$this->messages['update_ok']);
            } else {
                return array('status' => true, 'message'=>$this->messages['update_empty']);
            }
        } catch (PDOException $e) {
            return array(
                'status' => false,
                'message' => $this->messages['exception'],
//                'exception' => $e->getMessage()
            );
        }
    }

    public function delete($params)
    {
        $params = $this->removeInvalidFields($params);
        if (empty($params)) {
           return array('status' => false, 'message' => $this->messages['delete_no_param']) ;
        }

        $fields = array_keys($params);
        $values = array_values($params);
        $sql = "DELETE FROM {$this->table} WHERE ";
        foreach($fields as $k => $field) {
            $sql .= $field . " = ? ";
            if($k < count($fields)-1) {
                $sql .= ' AND ';
            }
        }

        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute($values);
            if($stmt->rowCount()){
                return array('status' => true, 'message' => $this->messages['delete_ok']);
            } else {
                return array('status' => false, 'message' => $this->messages['select_empty']);
            }
        } catch (PDOException $e) {
            return array(
                'status' => false,
                'message' => $this->messages['exception']
            );
        }
    }

    //apenas atualiza o campo excluido da tabela
    public function deleteUpdate($params)
    {
        $params = $this->removeInvalidFields($params);
        return $this->update(array('excluido' => 'S'), $params);
    }

    private function removeInvalidFields($params)
    {
        $valid_params = array();
        foreach($params as $field => $value) {
            $field = strtoupper($field);
            if(in_array($field, $this->columns)) $valid_params[$field] = $value;
        }
        return $valid_params;
    }

    private function checkNotNullColumns($params)
    {
        foreach($this->columns_not_null as $column) {
            //se nao existe a coluna dentro dos parametros
            if(!array_key_exists($column, $params)) {
                return false;
            } else {
                //se existe a coluna nos parametros, mas ela esta vazia
                $value = $params[$column];
                if(is_null($value)) {
                    return false;
                }
            }
        }
        return true;
    }
}