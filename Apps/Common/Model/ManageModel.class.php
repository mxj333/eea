<?php
namespace Common\Model;
use Think\Model;
class ManageModel extends Model {

    // pdo 数据库连接句柄
    protected $pdo_handel = '';
    protected $comparison = array('eq'=>'=','neq'=>'<>','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=','notlike'=>'NOT LIKE','like'=>'LIKE','in'=>'IN','notin'=>'NOT IN');

    public function _initialize() {
        // 初始化
        $this->pdo_handel = new \Org\Db\Driver\Pdo();
    }

    /**
     * 添加单条记录
     * $data  array
     */
    public function insert($data) {

        if (empty($data)) {
            return false;
        }

        // 初始化
        $fields = array();
        $values = array();
        $num = 0;

		$pk = $this->getPk();

        // 拼凑 sql 参数
        foreach ($data as $prop => $val) {

			if ($pk == $prop) {
				// 入库过滤主键
				continue;
			}
            $fields[] = $prop;
            $values[] = $val;
            $num ++;
        }

        // 入库
        $sql = 'INSERT INTO ' . C('DB_PREFIX') . Tf2Xhx($this->name) . ' (' . implode(",", $fields) . ') VALUES (' . trim(str_repeat('?,', $num), ",") . ')';
        return $this->pdo_handel->insert($sql, $values);
    }

    /**
     * 添加多条记录
     * $config  array
     */
    public function insertAll($config) {

        if (empty($config['values']) || empty($config['fields'])) {
            return false;
        }

        // 字段
        if (!is_array($config['fields'])) {
            $config['fields'] = explode(',', $config['fields']);
        }

        // 初始化
        $values = array();
        // values 值
        $sql_values = array();
        foreach ($config['values'] as $data){
            $sql_value   =  array();
            foreach ($config['fields'] as $field_key){
                if(is_scalar($data[$field_key])) { // 过滤非标量数据
                    $sql_value[] = '?';
                    $values[] = $data[$field_key];
                }
            }
            $sql_values[]    = '('.implode(',', $sql_value).')';
        }

        // 入库
        $sql='INSERT INTO ' . C('DB_PREFIX') . Tf2Xhx($this->name) . ' (' . implode(",", $config['fields']) . ') VALUES ' . implode(",", $sql_values);

        return $this->pdo_handel->insert($sql, $values);
    }

    /**
     * 修改记录
     * $config  array  数据
     * $expression 表达式语句
     */
    public function update($data, $config = array(), $expression = '') {

        if (empty($data) && !$expression) {
            // 没有数据不修改
            return false;
        }

        if (!$config['where']) {
            // 条件不存在  默认走主键
            $pk = $this->getPk();
            $config['where'] = array($pk => intval($data[$pk]));
        }

        // 初始化
        $fields = array();
        $where = array();
        $values = array();

        if (!is_array($config['where'])) {
            $config['where'] = explode(',', $config['where']);
        }

        // 拼凑参数
        foreach($data as $field_name => $field_value) {
            // 修改条件字段
            $setItem = $this->parseWhereItem($field_name, $field_value);
            $fields[] = $setItem['fields'];
            $values[] = $setItem['values'];
        }

        // 表达式语句
        if ($expression) {
            if (is_array($expression)) {
                $expr_sql = array();
                foreach ($expression as $expr_fields => $expr_val) {
                    $expr_sql[] = $expr_fields . '=' . $expr_val;
                }
                $fields[] = implode(',', $expr_sql);
            } else {
                $fields[] = $expression;
            }
        }

        // 拼凑 where 条件
        foreach ($config['where'] as $field => $val) {
            $whereItem = $this->parseWhereItem($field, $val);
            $where[] = $whereItem['fields'];
            $operate[] = $whereItem['operate'];
            // between and values 是数组
            if (is_array($whereItem['values'])) {
                $values = array_merge($values, $whereItem['values']);
            } else {
                $values[] = $whereItem['values'];
            }
        }

        // 修改
        $sql = 'UPDATE ' . C('DB_PREFIX') . Tf2Xhx($this->name) . ' SET ' . trim(implode(',', $fields), ',') . ' WHERE ';
        // where 条件
        $sql .= $this->whereToSql($where, $operate);

        // 排序
        if ($config['order']) {
            $sql .= $this->parseOrder($config['order']);
        }

        // 分页 limit
        if ($config['limit']) {
            if (is_array($config['limit'])) {
                $sql .= ' LIMIT ' . implode(',', $config['limit']);
            } else {
                $sql .= ' LIMIT ' . $config['limit'];
            }
        }

        return $this->pdo_handel->update($sql,$values);
    }

    /**
     * 删除记录
     * $config  array 
     */
    public function delete($data) {

        if (empty($data)) {
            // 没数据不处理
            return false;
        }
        
        if (!is_array($data)) {
            $pk = $this->getPk();
            $config['where'] = array($pk => array('IN', explode(',', $data)));
        } else {
            $config = $data;
        }

        // 初始化
        $fields = array();
        $values = array();

        // where 条件
        foreach ($config['where'] as $key => $val) {
            $whereItem = $this->parseWhereItem($key, $val);
            $fields[] = $whereItem['fields'];
            $operate[] = $whereItem['operate'];
            // between and values 是数组
            if (is_array($whereItem['values'])) {
                $values = array_merge($values, $whereItem['values']);
            } else {
                $values[] = $whereItem['values'];
            }
        }

        // sql 语句
        $sql = 'DELETE FROM ' . C('DB_PREFIX') . Tf2Xhx($this->name) . ' WHERE ';

        // where 条件
        $sql .= $this->whereToSql($fields, $operate);

        // 排序
        if ($config['order']) {
            $sql .= $this->parseOrder($config['order']);
        }

        // 分页 limit
        if ($config['limit']) {
            if (is_array($config['limit'])) {
                $sql .= ' LIMIT ' . implode(',', $config['limit']);
            } else {
                $sql .= ' LIMIT ' . $config['limit'];
            }
        }

        return $this->pdo_handel->delete($sql, $values);
    }

    public function getOne($data) {
        
        if (empty($data)) {
            // 没数据 直接返回
            return false;
        }
        
        $config = array(
            'fields' => '*',
        );

        if (is_array($data)) {
            $config = array_merge($config, $data);
        } else {
            $pk = $this->getPk();
            $config['where'] = array($pk => intval($data));
        }

        // fields 字段处理
        $sql_fields = $this->parseField($config['fields']);

        // 表明处理
        $sql_table = $this->parseTable($config['table']);

        // join 连表
        if ($config['join']) {
            if (is_array($config['join'])) {
                $sql_join = $this->parseJoin($config['join']);
            } else {
                $sql_join = ' ' . $config['join'] . ' ';
            }
        } 

        // 初始化
        $fields = array();
        $values = array();

        // where 条件
        foreach ($config['where'] as $key => $val) {
            $whereItem = $this->parseWhereItem($key, $val);
            $fields[] = $whereItem['fields'];
            $operate[] = $whereItem['operate'];
            // between and values 是数组
            if (is_array($whereItem['values'])) {
                $values = array_merge($values, $whereItem['values']);
            } else {
                $values[] = $whereItem['values'];
            }
        }

        $sql = 'SELECT '. $sql_fields;
        // 表
        $sql .= ' FROM ' . $sql_table . $sql_join . ' WHERE ';
        // 查询条件
        if (empty($config['where'])) {
            $sql .= 1;
        } elseif (is_array($config['where'])) {
            // where 条件
            $sql .= $this->whereToSql($fields, $operate);
        } else {
            $sql .= $config['where'];
        }

        // 排序字段
        if ($config['order']) {
            $sql .= $this->parseOrder($config['order']);
        }

        $sql .= ' LIMIT 1 ';
        
        $result = $this->pdo_handel->getRow($sql, $values);

        $tmp = count($result);
        switch ($tmp) {
            case 1:
                foreach ($result as $value) {
                    $res = $value;
                }
                break;
            default:
                $res = $result;
                break;
        }

        return $res;
    }

    public function total($config = array()) {

        // 表明处理
        $sql_table = $this->parseTable($config['table']);

        // join 连表
        if ($config['join']) {
            if (is_array($config['join'])) {
                $sql_join = $this->parseJoin($config['join']);
            } else {
                $sql_join = ' ' . $config['join'] . ' ';
            }
        } 

        // 初始化
        $fields = array();
        $values = array();

        // where 条件
        foreach ($config['where'] as $key => $val) {
            $whereItem = $this->parseWhereItem($key, $val);
            $fields[] = $whereItem['fields'];
            $operate[] = $whereItem['operate'];
            // between and values 是数组
            if (is_array($whereItem['values'])) {
                $values = array_merge($values, $whereItem['values']);
            } else {
                $values[] = $whereItem['values'];
            }
        }

        $sql = 'SELECT COUNT(1) ';
        // 表
        $sql .= ' FROM ' . $sql_table . $sql_join . ' WHERE ';
        // 查询条件
        if (empty($config['where'])) {
            $sql .= 1;
        } elseif (is_array($config['where'])) {
            // where 条件
            $sql .= $this->whereToSql($fields, $operate);
        } else {
            $sql .= $config['where'];
        }
        // 分组
        if ($config['group']) {
            $sql .= $this->parseGroup($config['group']);
        }
        $sql .= ' LIMIT 1';

        return $this->pdo_handel->getOne($sql, $values);
    }

    public function getAll($config = array()) {

        $default = array(
            'is_deal_result' => true,
            'order' => '',
            'where' => '1',
            'fields' => '*',
            'limit' => '',
        );

        $config = array_merge($default, $config);
        
        // fields 字段处理
        $sql_fields = $this->parseField($config['fields']);

        // 表明处理
        $sql_table = $this->parseTable($config['table']);

        // join 连表
        if ($config['join']) {
            if (is_array($config['join'])) {
                $sql_join = $this->parseJoin($config['join']);
            } else {
                $sql_join = ' ' . $config['join'] . ' ';
            }
        } 

        // 初始化
        $fields = array();
        $values = array();

        // where 条件
        foreach ($config['where'] as $key => $val) {
            $whereItem = $this->parseWhereItem($key, $val);
            $fields[] = $whereItem['fields'];
            $operate[] = $whereItem['operate'];
            // between and values 是数组
            if (is_array($whereItem['values'])) {
                $values = array_merge($values, $whereItem['values']);
            } else {
                $values[] = $whereItem['values'];
            }
        }

        $sql = 'SELECT '. $sql_fields;
        // 表
        $sql .= ' FROM ' . $sql_table . $sql_join . ' WHERE ';
        // 查询条件
        if (empty($config['where'])) {
            $sql .= 1;
        } elseif (is_array($config['where'])) {
            // where 条件
            $sql .= $this->whereToSql($fields, $operate);
        } else {
            $sql .= $config['where'];
        }
        // 分页取数据
        //if ($config['limit']) {
        //    $limit = explode(',', $config['limit']);
        //    $limit_num = count($limit);
        //}
        //if ($limit_num > 1) {
            // TO DO 这个需要通过 redis 获取 between and  之间的值
            // 查询搜索时 redis 也要查询搜索出对应的 id
            //$sql .= ' AND ' . $this->getPk() . ' between ' . $limit[0] . ' and ' . intval($limit[0] + $limit[1]);
        //}
        // 分页 limit
        //if ($limit_num > 1) {
        //    $sql .= ' limit ' . $limit[1];
        //} elseif ($limit_num == 1) {
        //    $sql .= ' limit ' . $limit[0];
        //}
        // 分组
        if ($config['group']) {
            $sql .= $this->parseGroup($config['group']);
        }
        // 排序
        if ($config['order']) {
            $sql .= $this->parseOrder($config['order']);
        }
        // 分页 limit
        if ($config['limit']) {
            if (is_array($config['limit'])) {
                $sql .= ' LIMIT ' . implode(',', $config['limit']);
            } else {
                $sql .= ' LIMIT ' . $config['limit'];
            }
        }
        
        $result = $this->pdo_handel->getAll($sql, $values);

        // 结果处理
        if (!is_array($config['fields'])) {
            $tmpFields = explode(',', $config['fields']);
        } else {
            $tmpFields = $config['fields'];
        }
        $tmp = $config['fields'] == '*' ? 0 : count($tmpFields);
        $firstField = reset($tmpFields);
        switch ($tmp) {
            case 1:
                foreach ($result as $value) {
                    $res[] = $value[$firstField];
                }
                break;
            case 2:
                $secondField = next($tmpFields);
                foreach ($result as $value) {
                    $res[$value[$firstField]] = $value[$secondField];
                }
                break;
            default:
                if ($config['fields'] == '*' || $config['is_api']) {
                    $res = $result;
                } else {
                    foreach ($result as $value) {
                        $res[$value[$firstField]] = $value;
                    }
                }
                break;
        }

        return $res;
    }

    // query 取一条
    public function queryFetch($sql, $values = null) {

        return $this->pdo_handel->query($sql, $values)->next();
    }

    // query 取所有
    public function queryFetchAll($sql, $values = null) {

        return $this->pdo_handel->query($sql, $values)->getAllRows();
    }

    // query 计数
    public function queryCount($sql, $values = null) {

        return $this->pdo_handel->query($sql, $values)->count();
    }

    // query 列表数
    public function queryColumnCount($sql, $values = null) {

        return $this->pdo_handel->query($sql, $values)->columnCount();
    }

    // query 语句执行
    public function querySql($sql) {

        return $this->pdo_handel->querySql($sql);
    }

    // 给一个字段增加一个单位值
    public function increase($fieldName, $where, $unit = 1) {

        if (!$fieldName || !$where) {
            return false;
        }

        // 修改的字段
        $set_sql = '';
        if (is_array($fieldName)) {
            foreach ($fieldName as $key => $val) {
                $set_sql .= ',' . $val . '=' . $val . '+';
                if (is_array($unit)) {
                    $set_sql .= $unit[$key];
                } else {
                    $set_sql .= $unit;
                }
            }
            $set_sql = substr($set_sql, 1);
        } else {
            $set_sql = $fieldName . '=' . $fieldName . '+' . $unit;
        }

        // 初始化
        $fields = array();
        $values = array();

        // where 条件
        foreach ($where as $key => $val) {
            $whereItem = $this->parseWhereItem($key, $val);
            $fields[] = $whereItem['fields'];
            $operate[] = $whereItem['operate'];
            // between and values 是数组
            if (is_array($whereItem['values'])) {
                $values = array_merge($values, $whereItem['values']);
            } else {
                $values[] = $whereItem['values'];
            }
        }

        // 修改
        $sql='UPDATE ' . C('DB_PREFIX') . Tf2Xhx($this->name) . ' SET ' . $set_sql . ' WHERE ';
        
        // where 条件
        $sql .= $this->whereToSql($fields, $operate);

        return $this->pdo_handel->update($sql, $values);
    }

    // 给一个字段减少一个单位值
    public function decrease($fieldName, $where, $unit = 1) {

        if (!$fieldName || !$where) {
            return false;
        }

        // 修改的字段
        $set_sql = '';
        if (is_array($fieldName)) {
            foreach ($fieldName as $key => $val) {
                if (is_array($unit)) {
                    $set_unit = $unit[$key];
                } else {
                    $set_unit = $unit;
                }
                $set_sql .= ',' . $val . '=IF(' . $val . ' > ' . $set_unit . ', ' . $val . ' - ' . $set_unit . ', 0)';
            }
            $set_sql = substr($set_sql, 1);
        } else {
            $set_sql = $fieldName . '=IF(' . $fieldName . ' > ' . $unit . ', ' . $fieldName . ' - ' . $unit . ', 0)';
        }

        // 初始化
        $fields = array();
        $values = array();

        // where 条件
        foreach ($where as $key => $val) {
            $whereItem = $this->parseWhereItem($key, $val);
            $fields[] = $whereItem['fields'];
            $operate[] = $whereItem['operate'];
            // between and values 是数组
            if (is_array($whereItem['values'])) {
                $values = array_merge($values, $whereItem['values']);
            } else {
                $values[] = $whereItem['values'];
            }
        }

        // 修改
        $sql='UPDATE ' . C('DB_PREFIX') . Tf2Xhx($this->name) . ' SET ' . $set_sql . ' WHERE ';
        
        // where 条件
        $sql .= $this->whereToSql($fields, $operate);

        return $this->pdo_handel->update($sql,$values);
    }

    /**
     * where子单元分析
     * 情况一 array('id' => 1)
     * 情况二 array('id' => array('IN', array(1,2,3)))
     * 情况三 array('id' => array('IN', '1,2,3'))
     * 情况四 array('name' => array('LIKE', '%abc%'))
     * 情况五 array('id' => array('eq', 10))
     */
    protected function parseWhereItem($key, $val) {
        // 初始化
        $fields = '';
        $values = '';

        if (is_array($val)) {

            $operate = isset($val['_logic']) ? strtoupper($val['_logic']) : '';
            if(in_array($operate, array('AND', 'OR', 'XOR'))){
                // 定义逻辑运算规则 例如 OR XOR AND NOT
                $operate = ' ' . $operate . ' ';
                unset($val['_logic']);
            }else{
                // 默认进行 AND 运算
                $operate = ' AND ';
            }

            if (strtolower($val[0]) == 'in') {
                $fields = ' find_in_set(' . $key .', ?) ';
                if (is_array($val[1])) {
                    $values = implode(',', $val[1]);
                } else {
                    $values = $val[1];
                }
            } elseif (strtolower($val[0]) == 'like') {
                $fields = ' ' . $key . ' LIKE ? ';
                $values = $val[1];
            } elseif (in_array(strtolower($val[0]), array('between', 'not between'))) {
                $data = is_string($val[1])? explode(',',$val[1]):$val[1];
                $fields =  $key . ' ' . strtoupper($val[0]) . ' ? AND ? ';
                $values = array($data[0], $data[1]);
            } elseif (in_array(strtolower($val[0]), array('eq', 'neq', 'gt', 'egt', 'lt', 'elt'))) {
                $fields = ' ' . $key . ' ' . $this->comparison[strtolower($val[0])] . ' ? ';
                $values = $val[1];
            } elseif (in_array(strtolower($val[0]), array('=', '!=', '>', '>=', '<', '<='))) {
                $fields = ' ' . $key . ' ' . strtolower($val[0]) . ' ? ';
                $values = $val[1];
            } else {
                // 用到哪个扩充哪个
                // 默认 返回
                $fields = ' ' . $key . ' = ? ';
                $values = $val[0];
            }
        } else {
            $fields = ' ' .$key . ' = ? ';
            $values = $val;
            // 默认进行 AND 运算
            $operate = ' AND ';
        }

        return array(
            'fields' =>  $fields,
            'operate' => $operate,
            'values' => $values
        );
    }

    public function whereToSql($fields, $operate) {

        $sql = '';
        foreach ($fields as $key => $val) {
            $sql .= ' ' . $val . ' ' . $operate[$key];
        }

        if ($sql) {
            return substr($sql, 0, -strlen($operate[$key]));
        }

        return '';
    }

    /**
     * order分析
     * 情况一 ' sort DESC '
     * 情况二 array('id', 'sort' => 'desc')
     */
    protected function parseOrder($order) {
        if (is_array($order)) {
            $array   =  array();
            foreach ($order as $key => $val){
                if(is_numeric($key)) {
                    $array[] =  $val;
                }else{
                    $array[] =  $key . ' ' . $val;
                }
            }
            $order   =  implode(',', $array);
        }
        return !empty($order) ?  ' ORDER BY ' . $order : '';
    }

    /**
     * group分析
     */
    protected function parseGroup($group) {
        return !empty($group)? ' GROUP BY '.$group:'';
    }

    /**
     * field分析
     * 支持 'field1'=>'field2' 这样的字段别名定义
     */
    protected function parseField($fields) {
        if(is_string($fields) && strpos($fields,',')) {
            $fields    = explode(',',$fields);
        }
        if(is_array($fields)) {
            // 完善数组方式传字段名的支持
            $array   =  array();
            foreach ($fields as $key=>$field){
                if(!is_numeric($key)) {
                    $array[] =  $key . ' AS ' . $field;
                } else {
                    $array[] =  $field;
                }
            }
            $fieldsStr = implode(',', $array);
        }elseif(is_string($fields) && !empty($fields)) {
            $fieldsStr = $fields;
        }else{
            $fieldsStr = '*';
        }
        //TODO 如果是查询全部字段，并且是join的方式，那么就把要查的表加个别名，以免字段被覆盖
        return $fieldsStr;
    }

    /**
     * join分析
     * @access protected
     * @param array $join
     * @return string
     */
    protected function parseJoin($join) {
        $joinStr = '';
        if(!empty($join)) {
            $joinStr    =   ' ' . implode(' ', $join) . ' ';
        }
        return $joinStr;
    }

    /**
     * table分析
     * @access protected
     * @param mixed $table
     * @return string
     */
    protected function parseTable($tables) {
        if (!$tables) {
            // 默认当前控制器对应的表名
            return C('DB_PREFIX') . Tf2Xhx($this->name);
        }

        if (is_array($tables)) {
            // 支持别名定义
            $array   =  array();
            foreach ($tables as $table => $alias){
                if(!is_numeric($table)) {
                    $array[] =  $table . ' ' . $alias;
                } else {
                    $array[] =  $table;
                }
            }
            $tables  =  $array;
        } elseif (is_string($tables)) {
            $tables  =  explode(',', $tables);
        }
        $tables = implode(',', $tables);
        return $tables;
    }

    /**
     * 自动验证规则 字符串长度是否超出范围（包含临界值）
     */
    public function checkLength($string, $min_length = 2, $max_length = 10) {

        // 字符串长度
        $string_length = get_string_total_length($string);

        // 判断是否小于最小范围
        if ($min_length !== false && $string_length < $min_length) {
            return false;
        }

        // 判断是否超出最大范围
        if ($max_length !== false && $string_length > $max_length) {
            return false;
        }

        return true;
    }

    /**
     * 自动验证规则 数字大小是否超出范围（包含临界值）
     */
    public function numberComparison($number, $min = 2, $max = 10) {

        // 判断是否小于最小范围
        if ($min !== false && $number < $min) {
            return false;
        }

        // 判断是否超出最大范围
        if ($max !== false && $number > $max) {
            return false;
        }

        return true;
    }

    /**
     * 自动验证规则 检查字符串是否为指定类型
     * type 1 汉字 or 数字 or 英文
     *      2 汉字 or 数字
     *      3 汉字 or 英文
     *      4 数字 or 英文
     *      5 纯汉字
     *      6 纯数字
     *      7 纯英文
     */
    public function checkString($string, $type = 1) {
        
        // 汉字
        $chinese = preg_match('/['.chr(0xa1).'-'.chr(0xff).']/', $string);
        // 数字
        $number = preg_match('/[0-9]/', $string);
        // 字母
        $word = preg_match('/[a-zA-Z]/', $string);

        // 汉字 or 数字 or 英文
        if ($type == 1 && ($chinese || $number || $word)) {
            return true;
        }

        // 汉字 or 数字
        if ($type == 2 && !$word && ($chinese || $number)) {
            return true;
        }

        // 汉字 or 英文
        if ($type == 3 && !$number && ($chinese || $word)) {
            return true;
        }

        // 数字 or 英文
        if ($type == 4 && !$chinese && ($number || $word)) {
            return true;
        }

        // 纯汉字
        if ($type == 5 && $chinese && !$number && !$word) {
            return true;
        }

        // 纯数字
        if ($type == 6 && !$chinese && $number && !$word) {
            return true;
        }

        // 纯英文
        if ($type == 7 && !$chinese && !$number && $word) {
            return true;
        }
        
        return false;
    }

    public function dealTitleDelimiter($string) {
        if (!$string) {
            return '';
        }
        // -中国-北京  过滤 第一个 -
        if ($string[0] == '-') {
            $string = substr($string, 1);
        }
        // 中国-北京-全部  过滤 -全部
        if (substr($string, -7) == '-全部') {
            $string = substr($string, 0, -7);
        }
        
        return $string;
    }

    public function dealValueDelimiter($string) {
        if (!$string) {
            return '';
        }
        // -1-2  过滤 第一个 -
        if ($string[0] == '-') {
            $string = substr($string, 1);
        }
        // 1-2-0  过滤 -0
        if (substr($string, -2) == '-0') {
            $string = substr($string, 0, -2);
        }
        
        return $string;
    }
}