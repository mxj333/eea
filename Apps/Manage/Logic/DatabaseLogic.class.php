<?php
namespace Manage\Logic;
class DatabaseLogic extends Logic {

    public function getDbList() {
        if (!$dbs = $_SESSION['_databaseList_']) {
            $dbs = D('Database', 'Model')->queryFetchAll('SHOW DATABASES');
            $_SESSION['_databaseList_'] = $dbs;
        }
        return $dbs;
    }

    public function getUseDb() {
        if ($_SESSION['useDb']) {
            $dbName = $_SESSION['useDb'];
        } else {
            $dbName = C('DB_NAME');
            $_SESSION['useDb'] = $dbName;
        }
        return $dbName;
    }

    public function getSelectDb() {
        return $_SESSION['selectDb'] ? $_SESSION['selectDb'] : $_SESSION['useDb'];
    }

    public function getTables($dbName) {
        $_SESSION['selectDb'] = $dbName;
        // 获取数据库的表列表
        $result = D('Database', 'Model')->queryFetchAll('SHOW TABLES FROM ' . $dbName);
        $tables = array();
        foreach ($result as $key => $val) {
            $tables[$key] = current($val);
        }
        return $tables;
    }

    public function updateTable($dbName, $tableName, $engine, $comment, $charset, $collation) {
        D('Database', 'Model')->querySql('Use ' . $dbName . ';' . 'ALTER TABLE `' . $tableName . '` COMMENT="' . $comment . '" ENGINE="' . $engine . '" DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collation);
        return true;
    }
    public function createDatabase($dbName, $charset, $collation) {
        $sql = 'CREATE DATABASE `' . $dbName . '` DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collation . ';';
        $result = D('Database', 'Model')->queryCount($sql);
        $_SESSION['_databaseList_'] = '';
        return $result;
    }

    public function getTableStatus($dbName = '', $tableName = '') {
        $dbName = $dbName ? $dbName : $_SESSION['selectDb'];
        $_SESSION['selectDb'] = $dbName;
        $sql = 'SHOW TABLE STATUS FROM ' . $dbName;
        if ($tableName) {
            $sql .= ' WHERE Name="' . $tableName . '"';
        }

        $result = D('Database', 'Model')->queryFetchAll($sql);

        foreach ($result as &$value) {
            $value['Data_free'] = byteFormat($value['Data_free']);
        }
        return $result;
    }

    public function createTable($dbName, $data) {

        if (empty($data['tableName'])) {
            return false;
        }

        $createSql = 'Use ' . $dbName . ';' . ' CREATE TABLE `' . $data['tableName'] . '` (';
        $len = count($data['name']);
        for ($i = 0; $i < $len; $i++) {
            if (!empty($data['name'][$i])) {
                $createSql .= '`' . $data['name'][$i] . '` ' . $data['type'][$i];

                if ($data['length'][$i]) {
                    $createSql .= ' (' . $data['length'][$i] . ') ';
                }
                if ($data['attribute'][$i]) {
                    $createSql .= ' ' . $data['attribute'][$i] . ' ';
                }
                $createSql .= ' ' . $data['null'][$i] . ' ';
                if ($data['default'][$i]) {
                    $createSql .= ' DEFAULT ' . $data['default'][$i] . ' ';
                }
                if ($data['autoinc'][$i]) {
                    $createSql .= ' ' . $data['autoinc'][$i] . ' ';
                }
                if ($data['comment'][$i]) {
                    $createSql .= ' COMMENT "' . $data['comment'][$i] . '" ';
                }
                $createSql .= ',';
            }
        }
        for ($i = 0; $i < $len; $i ++) {
            if (!empty($data['extra'][$i])) {
                $createSql .= $data['extra'][$i] . ' ( `' . $data['name'][$i] . '`) ,';
            }
        }
        $createSql = substr($createSql, 0, -1);
        $createSql .= ' ) ENGINE = ' . $data['Engine'] . ' CHARACTER SET ' . $data['Charset'] . '  COMMENT = "' . $data['Comment'] . '"';

        D('Database', 'Model')->querySql($createSql);
        return true;
    }

    public function cloneTable($sourceDb, $sourceTable, $dbName, $tableName, $option = 0) {

        // 获取表结构
        $info = D('Database', 'Model')->queryFetch('SHOW CREATE TABLE ' . $sourceDb . '.`' . $sourceTable . '`');

        $sql = $info['Create Table'];
        $sql = 'USE ' . $dbName . ';' . preg_replace('/CREATE TABLE\s`' . $sourceTable . '`/is', 'CREATE TABLE `' . $tableName . '`', $sql);

        // 开始复制
        $result = D('Database', 'Model')->querySql($sql);

        if ($option) {
            // 复制表数据
            $sql = 'INSERT INTO `' . $dbName . '`.`' . $tableName . '` SELECT * FROM `' . $sourceDb . '`.`' . $sourceTable . '` ;';
            $result = D('Database', 'Model')->querySql($sql);
        }
        return true;
    }

    public function deleteTable($dbName, $tableName) {
        $sql = 'USE ' . $dbName . ';' . 'DROP TABLE `' . $tableName . '`;';
        D('Database', 'Model')->querySql($sql);
        return true;
    }

    public function clearTable($dbName, $tableName) {
        $sql = 'USE ' . $dbName . ';' . 'TRUNCATE TABLE `' . $tableName . '`;';
        D('Database', 'Model')->querySql($sql);
        return true;
    }

    public function deleteDatabase($dbName) {
        $sql = 'DROP DATABASE ' . $dbName . ';';
        D('Database', 'Model')->querySql($sql);
        $_SESSION['_databaseList_'] = '';
        return true;
    }

    public function getSqlData($dbName, $sql) {

        $tags = 'INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|LOAD DATA|SELECT .* INTO|COPY|ALTER|GRANT|TRUNCATE|REVOKE|LOCK|UNLOCK';

        $startTime = microtime(TRUE);
        D('Database', 'Model')->querySql('USE ' . $dbName . ';');
        if (preg_match('/^\s*"?(' . $tags . ')\s+/i', $sql)) {
            $result = D('Database', 'Model')->queryCount($sql);
        } else {
            $res = D('Database', 'Model')->queryFetchAll($sql);
            $fields = array_keys($res[0]);
            $result[] = $fields;
            foreach ($res as $key => $val) {
                $val  = array_values($val);
                $result[] = $val;
            }
        }
        $runtime = number_format((microtime(TRUE) - $startTime), 6);
        D('Database', 'Model')->querySql('INSERT INTO ' . $_SESSION['useDb'] . '.dkt_db_sql_log(dsl_sql,dsl_created,dsl_runtime,u_id) VALUES ("' . $sql . '",' . time() . ', ' . $runtime . ', ' . $_SESSION[C('USER_AUTH_KEY')] . ');');
        return $result;
    }
}