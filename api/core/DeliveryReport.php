<?php
include_once API_PATH . '/api/core/DbPdo.php';

class DeliveryReport {
    function __construct() {
        $objDb = new DbPdo();
        $this->cn = $objDb->connect();
    }
    function callbackTransaction($data = array()){
        $cn = $this->cn;
        $cn->beginTransaction();
        try{
            $this->saveData($data);
            $cn->commit();
        } catch (Exception $ex){
            $cn->rollBack();
            throw new Exception($ex->getMessage());
        }
    }
    function saveData($data = array()){
        $cn = $this->cn;

        $fields = $values = $parameters = array();
        foreach($data as $k => $d){
            $fields[$k] = $k;
            $values[$k] = ":{$k}";
            $parameters[$k] = $d;
        }
        $sql = "INSERT INTO delivery_report (" . implode(",", $fields) . ") VALUES (" . implode(',', $values) . ");";
        $stmt = $cn->prepare($sql);
        $stmt->execute($parameters);
        return $cn->lastInsertId('id');
    }
}