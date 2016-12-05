<?php

class MongoClient {

    private $manager = null;
    private $dbName = null;
    static $mongos = [];

    static function getInstance($connection, $dbName) {
        $key = md5($connection . $dbName);
        if(!isset(self::$mongos[$key])) {
            self::$mongos[$key] = new MongoClient($connection, $dbName);
        }
        return self::$mongos[$key];
    }

    function __construct($connection, $dbName) {
        $this->dbName = $dbName;
        $this->manager = new \MongoDB\Driver\Manager($connection);
    }

    function insert($coll, $data) {
        $bulk = new \MongoDB\Driver\BulkWrite();
        $bulk->insert($data);
        $result = $this->manager->executeBulkWrite($this->dbName . "." . $coll, $bulk);
        return $result;
    }

    function update($coll, $newData, $filters) {
        $bulk = new \MongoDB\Driver\BulkWrite();
        $bulk->update($filters, $newData, ['multi' => false]);
        $result = $this->manager->executeBulkWrite($this->dbName . "." . $coll, $bulk);
        return $result;
    }

    function delete($coll, $filters) {
        $bulk = new \MongoDB\Driver\BulkWrite();
        $bulk->delete($filters);
        $result = $this->manager->executeBulkWrite($this->dbName . "." . $coll, $bulk);
        return $result;
    }

    function query($coll, $condition, $option=[]) {
        $queryObj = new \MongoDB\Driver\Query($condition, $option);
        $cursor = $this->manager->executeQuery($this->dbName . "." . $coll, $queryObj);
        $result = $cursor->toArray();
        return $result;
    }

    function queryOne($coll, $condition, $option) {
        $result = $this->query($coll, $condition, $option);
        return empty($result[0]) ? [] : $result[0];
    }

    function obj2json($obj) {
        return empty($obj) ? [] : json_decode(json_encode($obj), true);
    }
}
