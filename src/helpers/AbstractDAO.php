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

    public function destroyPdo()
    {
        $this->pdo = null;
        Database::destroyInstance();
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
        return $this->messages;
    }

    public function setMessage($name, $value)
    {
        $this->messages[$name] = $value;
    }

    public function query($params = array())
    {
        $sql = "SELECT * FROM {$this->table}";

        $paramsOrderBy = '';
        if(array_key_exists('orderby', $params)) {
            $paramsOrderBy = $params['orderby'];
            unset($params['orderby']);
        }

        $paramsLimit = '';
        if(array_key_exists('limit', $params)) {
            $paramsLimit = $params['limit'];
            unset($params['limit']);
        }

        $params = $this->removeInvalidFields($params);
        $fields = array_keys($params);
        $values = array_values($params);
        if(!empty($params)) {
            $sql .= " WHERE ";
            foreach($fields as $k=>$field) {
                $value = $values[$k];
                $aux_op = $value[0];

                switch ($aux_op) {
                    case '>':
                        $op = '>';
                        $value = str_replace('>','',$value);
                        break;
                    case '<':
                        $op = '<';
                        $value = str_replace('<','',$value);
                        break;
                    case '*':
                        $op = 'LIKE';
                        $value = str_replace('*','',$value);
                        $value = "%".$value."%";
                        break;
                    default:
                        $op = '=';
                        break;
                }
                $values[$k] = $value;
                $sql .= $field . " " . $op . " ? ";
                if($k < count($params)-1) {
                    $sql .= ' AND ';
                }
            }
        }

        //constroi order by
        if($paramsOrderBy != ''){
            $paramsOrderBy = explode(',',$paramsOrderBy);
            $sql = $this->buildOrderBy($sql, $paramsOrderBy);
        }

        //constroi limit
        if($paramsLimit != '') {
            $paramsLimit = explode(',', $paramsLimit);
            if(count($paramsLimit) >= 1) {
                $limit = $paramsLimit[0];
                $offset = null;
                if(count($paramsLimit) > 1) {
                    $offset = $paramsLimit[1];
                }
                $sql = $this->buildLimit($sql, $limit, $offset);
            }
        }
//        echo $sql;exit;
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute($values);
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
//                'exception'=>$e->getMessage()
            );
        }
    }
    private function buildLimit($sql, $limit, $offset = null)
    {
        if(is_numeric($limit)) {
            $sql .= " LIMIT " . $limit;
            if(is_numeric($offset)) {
                $sql .= " OFFSET " . $offset;
            }
        }
        return $sql;
    }
    private function buildOrderBy($sql, $params)
    {
        $finalParams = array();
        foreach($params as $k=>$param){

            $param = trim($param);
            $aux = $param[0];
            $param = str_replace('+', '', $param);
            $param = str_replace('-', '', $param);
            if(in_array($param, $this->columns)) {
                if($aux=='-'){
                    $param .= " DESC";
                }else{
                    $param .= " ASC";
                }
                $finalParams[] = $param;
            }
        }
        if(count($finalParams)) {
            $sql .= " ORDER BY " . implode(', ', $finalParams);
        }
        return $sql;
    }
    public function rawQuery($sql, $values)
    {
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
//                'exception'=>$e->getMessage()
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
        $nullColumns = $this->checkNotNullColumns($params);
        if(count($nullColumns)) {
            return array(
                'status' => false,
                'message' => "As colunas a seguir são obrigatórias e não foram informadas: ".implode(', ', $nullColumns)
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
                return array('status' => true, 'message' => $this->messages['insert_ok'], 'result' => $result['result']);
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
        $nullColumns = array();
        foreach($this->columns_not_null as $column) {
            //se nao existe a coluna dentro dos parametros
            if(!array_key_exists($column, $params)) {
                $nullColumns[] = $column;
            } else {
                //se existe a coluna nos parametros, mas ela esta vazia
                $value = $params[$column];
                if(is_null($value)) {
                    $nullColumns[] = $column;
                }
            }
        }
        return $nullColumns;
    }
    public static function getDateTimeNow()
    {
        return date('Y-m-d H:i:s');
    }
}