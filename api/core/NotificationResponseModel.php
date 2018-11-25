<?php
include_once API_PATH . '/api/core/DbPdo.php';

class NotificationResponseModel {
    function __construct() {
        $objDb = new DbPdo();
        $this->cn = $objDb->connect();
    }
    function responseTransaction($mo_data = array()){
        $cn = $this->cn;
        $cn->beginTransaction();
        try{
            $data = $this->buildData($mo_data);
            $this->saveData($data);
            $cn->commit();
        } catch (Exception $ex) {
            $cn->rollBack();
            throw new Exception($ex->getMessage());
        }
    }
    function buildData($mo_data = array()){
        $data['supplier_code'] = $mo_data['id'];
        $data['subaccount_name'] = $mo_data['subAccount'];
        $data['campaign_alias'] = $mo_data['campaignAlias'];
        $data['carrier_id'] = $mo_data['carrierId'];
        $data['carrier_name'] = $mo_data['carrierName'];
        $data['user_number'] = $mo_data['source'];
        $data['shortcode'] = $mo_data['shortCode'];
        $data['content'] = $mo_data['messageText'];
        $data['received_at'] = date('H:i:s', intval($mo_data['receivedAt'])/1000);
        $data['received_date'] = date('Y-m-d H:i:s', strtotime($mo_data['receivedDate']));
        $data['supplier_origin_code'] = $mo_data['mt']['id'];
        $data['notification_origin_id'] = $mo_data['mt']['correlationId'];
        $data['sender_name'] = $mo_data['mt']['username'];
        $data['sender_email'] = $mo_data['mt']['email'];
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = 1;

        return $data;
    }
    function saveData($data = array()){
        $cn = $this->cn;
        $fields = $values = $parameters = array();
        foreach($data as $k => $d){
            $fields[$k] = $k;
            $values[$k] = ":{$k}";
            $parameters[$k] = $d;
        }
        $sql = "INSERT INTO notification_response (" . (implode(",", $fields)) . ") VALUES (" . (implode(",", $values)) . ");";
        $stmt = $cn->prepare($sql);
        $stmt->execute($parameters);
        return $cn->lastInsertId();
    }
}