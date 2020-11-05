<?php


namespace app\model;


use app\engine\Db;

abstract class DBModel extends Model
{
    /*Фильтры
      Один элемент этого массива имеет структуру - [
          field - поле
          operator - оператор сравнения
          value - значение для сравнения
          connector - 'OR' или 'AND' для соединения нескольких условий
      ]
    */
    protected $where = [];

    //Сортировка
    protected $order = [];

    protected $props = [];

    protected $keyFieldName = 'id';

    protected $agrFunctions = ['AVG', 'COUNT', 'MAX', 'MIN', 'SUM'];

    protected $operators = ['<', '<=', '=', '>', '>=', '!='];

    protected function isProperties($name) {
         return $name == $this->keyFieldName || array_key_exists($name, $this->props);
    }

    public function __call($method, $parameters)
    {
        
        $up_method = strtoupper($method);

        //Агрегационный метод
        if (array_search($up_method, $this->agrFunctions) !== false) {
            return $this->agr_func($up_method, $parameters);
        }
        $connector = 'AND';

        switch ($method) {
            case 'orWhere': 
                $connector = 'OR';
            case 'where':
                return $this->where_func($parameters, $connector);
            case 'orderBy':
                return $this->order_func($parameters);
            default:
                return call_user_func_array([$this, $method], $parameters);    
        }
    }

    protected function where_func($parameters, $connector = 'AND') {
        $count_par = count($parameters);
        if ($count_par < 2 || $count_par > 3) {
            throw new \Exception("Недопустимое количество параметров метода where!");    
        }

        $field = $parameters[0];
        $operator = ($count_par == 2) ? '=' : $parameters[1];
        $value = $parameters[$count_par - 1];
        
        if (!$this->isProperties($field)) {
            throw new \Exception("Не существует поле {$field} для условной выборки!"); 
        }

        if (array_search($operator, $this->operators) === false) {
            throw new \Exception("Не верный оператор '{$operator}' передан в качестве параметра №2!"); 
        }

        $this->where[] = [
            'field' => $field,
            'operator' => $operator,
            'value' => $value,
            'connector' => $connector
        ];

        $result = clone $this;
        array_pop($this->where);

        return $result;
    
    }

    protected function order_func($parameters) {
        foreach ($parameters as $field) {
            $field_words = explode(" ",$field);
            if (!$this->isProperties($field_words[0])) {
                throw new \Exception("Не существует поля {$field_words[0]} для условной выборки!"); 
            }
        }

        $old_order = $this->order;
        $this->order = array_merge($this->order, $this->getFieldsForQuery($parameters)); 
        $result = clone $this;
        $this->order = $old_order;

        return $result;

    }

    protected function agr_func($method, $parameters) {
        
        $agr_params = $this->getFieldsForQuery($parameters);
        if (count($agr_params) == 0 && $method == 'COUNT') {
            $agr_params[] = '*';
        }

        if (count($agr_params) == 0) {
            throw new \Exception('У агрегационных методов должны быть параметры в качестве полей');
        } 


        $fields = array_map(function ($agr) use ($method) {
            return "{$method}({$agr})";
        }, $agr_params);


        $query = $this->getQuery($fields,[]);
        $result = Db::getInstance()->queryOne($query['sql'],$query['params']);
        return (count($result) == 1) ? $result[array_keys($result)[0]] : $result;
    }

    //Функция обратаывает массив полей для передачи SQL. Происходит проверка, есть ли такое поле, если есть обрамлляем в обратные кавычки. 
    //Также разрешено использовать вместе с именами полей DESC для сортировки и агрегатные функции
    protected function getFieldsForQuery($fields) {
        $result = [];

        foreach ($fields as $field) {

            $field_words = explode(" ",$field);
            if ($this->isProperties($field_words[0])) {
                $desc = (count($field_words) > 1 && $field_words[1]='DESC') ? " DESC" : "";
                $result[] = "`{$field_words[0]}`{$desc}";
                continue;
            }


            //Решил, что в полях fields разрешено использовать агрегирующие функции, поэтому проверим на них
            preg_match("/([a-z]+)\([\'\`]?([\*a-z]+)[\'\`]?\)/i", $field, $matches, PREG_OFFSET_CAPTURE);
            if (count($matches) == 3 && array_search($matches[1][0],$this->agrFunctions) !== false) {
                if ($this->isProperties($matches[2][0])) {
                    $result[] = "{$matches[1][0]}(`{$matches[2][0]}`) AS `{$matches[2][0]}`";    
                } elseif ($matches[2][0]=="*")  {
                    $result[] = "{$matches[1][0]}(*) AS {$matches[1][0]}";
                }
            }
        }

        //На выходе получаем массив полей для формирования запроса. Его можно смело передавать SQL не переживая за инъекции
        return $result;
    }

    //Формируем динамический запрос, возвращается массив из двух элементов ['sql' - текст запроса, 'params' - параметры]
    protected function getQuery($fields = [], $where = [], $limit = 0, $offset = 0) {
        $fields = $this->getFieldsForQuery($fields);
        $order = $this->getFieldsForQuery($this->order);

        $where = array_merge($where, $this->where);

        $query_params = [];
        $index = 0;
        $where_str = "";
        foreach ($where as $where_one) {
            if (array_search($where_one['operator'],$this->operators) === false || !$this->isProperties($where_one['field'])) {
                continue;
            }
            $param_name = "W_{$index}";
            $where_str .= sprintf(" %s `{$where_one['field']}` {$where_one['operator']} :{$param_name}",
                ($where_str == "") ? "" : (($where_one['operator'] = "OR") ? "OR" : "AND")
            );
            $query_params[$param_name] = $where_one["value"];
            $index++;
        }
        $where_str = ($where_str == "") ? "" : " WHERE {$where_str}";

        $fields_query = ($fields) ? implode(", ", $fields) : "*";
        $order_by = ($this->order) ? (" ORDER BY " . implode(", ", $this->order)) : "";
        $limit = ($limit > 0) ? (" LIMIT " . (int)$offset . ", " . (int)$limit)  : "";


        return 
        [
            "sql" => "SELECT {$fields_query} FROM {$this->getTableName()} {$where_str}{$order_by}{$limit}",
            "params" => $query_params
        ];
    }


    //Старый first стал теперь find, т.к. поменялась логика работы
    protected function first()
    {
        $query = $this->getQuery([], [], 1);
        return Db::getInstance()->queryObject($query['sql'], $query['params'], static::class);
    }

    protected function find($id)
    {
        $query = $this->getQuery([], [['field' => $this->keyFieldName, 'value' => $id, 'operator' => '=']], 1);
        return Db::getInstance()->queryObject($query['sql'], $query['params'], static::class);        
    }

    protected function get($limit=0, $offset=0)
    {
        $query = $this->getQuery([],[],$limit,$offset);
        return Db::getInstance()->queryAll($query['sql'],$query['params']);
    }


    public function insert() {

        $params = [];

        foreach ($this->props as $key=>$value) {
            $params["{$key}"] = $this->$key;
        }

        $columns = "`" . implode("`, `", array_keys($params)) . "`";
        $values = ":" . implode(", :", array_keys($params));

        $sql = "INSERT INTO {$this->getTableName()} ({$columns}) VALUES ({$values})";

        Db::getInstance()->execute($sql, $params);
        $id_name = $this->keyFieldName;
        $this->$id_name = Db::getInstance()->lastInsertId();

        return $this;
    }

    public function update() {
        $id_name = $this->keyFieldName;
        $sets = [];
        $params = [];

        foreach ($this->props as $key=>$value) {
            if ($value) {
                $params["{$key}"] = $this->$key;
                $sets[] = "`{$key}` = :{$key}";
            }    
        }
        var_dump($sets);
        var_dump($params);

        $id = $this->$id_name;

        if (!isset($id) || count($sets) == 0) {
            return $this;
        }

        $set_str = implode(", ", $sets);

        $sql = "UPDATE {$this->getTableName()} SET {$set_str} WHERE {$id_name} = '{$id}'";
        var_dump($sql);
        if (Db::getInstance()->execute($sql, $params)) {
            $this->clearProps();
        }
        return $this;
    }

    protected function clearProps() {
        foreach ($this->props as $key=>$value) {
            $this->props[$key] = false;
        }
    }

    public function save() {
        if (is_null($this->id))
            $this->insert();
        else
            $this->update();
    }

    public function delete() {
        $sql = "DELETE FROM {$this->getTableName()} WHERE id = :id";
        return Db::getInstance()->execute($sql, ['id' => $this->id])->rowCount();
    }

    abstract protected function getTableName();
}