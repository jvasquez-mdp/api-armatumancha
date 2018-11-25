<?php
include_once API_PATH . '/api/core/DbPdo.php';
include_once API_PATH . '/api/core/NotificationsModel.php';
include_once API_PATH . '/api/core/NotificationTypeModel.php';
include_once API_PATH . '/api/core/UsersModel.php';

class NotificationsModel {
    function __construct() {
        $objDb = new DbPdo();
        $this->cn = $objDb->connect();
    }
    function sendTransaction($data = array()){
        $objUsers = new UsersModel();
        $objNotificationsType = new NotificationTypeModel();

        $postfields = array();

        $cn = $this->cn;
        $cn->beginTransaction();
        try{
            $notification_alias = $data['notification'];
            $users = $data['users'];
            $usersDataList = $objUsers->getDataByIds($users);

            $notificationsTypeData = $objNotificationsType->getNotificationTypeByAlias($notification_alias);

            $notifications_add['type_id'] = $notificationsTypeData['id'];
            $postfields['messages'] = array();
            foreach ($usersDataList as $user) {
                $notifications_add['content'] = $objNotificationsType->getContentPro($notificationsTypeData['content'], $user);
                $notifications_detail_add['notification_id'] = $correlationId = $this->saveData($notifications_add);
                $notifications_detail_add['user_id'] = $user['user_id'];
                $this->saveReceiversData($notifications_detail_add);

                $postfields['messages'][$user['user_id']]['destination'] = $user['user_number'];
                $postfields['messages'][$user['user_id']]['messageText'] = $notifications_add['content'];
                $postfields['messages'][$user['user_id']]['correlationId'] = $correlationId;
            }
            $postfields['messages'] = array_values($postfields['messages']);
            $postfields['timeZone'] = 'America/Lima';

            $result = $this->sendNotification($postfields);

            if(!empty($result['error-message'])){
                throw new Exception(json_encode(array('error' => 'cURL error!!', 'error-content' => $result['error-message'])));
            }
            if(!empty($result['response'])){
                $response = json_decode($result['response'], true);
                if(!empty($response['messages'])){
                    foreach($response['messages'] as $rsp){
                        $this->updateSupplierCode($rsp);
                    }
                    $cn->commit();
                }else{
                    throw new Exception(json_encode(array('error' => 'messages not exists!!', 'error-content' => $response)));
                }
            }else{
                throw new Exception(json_encode(array('error' => 'response not exists!!', 'error-content' => $result)));
            }
        } catch (Exception $ex) {
            $cn->rollBack();
            throw new Exception($ex->getMessage());
        }
    }
    function saveData($data = array()){
        $cn = $this->cn;
        $sql = "INSERT INTO notifications (content, type_id, created_at, status) VALUES (:content, :type_id, :created_at, 1);";
        $stmt = $cn->prepare($sql);
        $stmt->execute(array(':content' => $data['content'], ':type_id' => $data['type_id'], ':created_at' => date('Y-m-d H:i:s')));
        return $cn->lastInsertId('id');
    }
    function saveReceiversData($data = array()){
        $cn = $this->cn;
        $sql = "INSERT INTO notification_receivers (notification_id, user_id, status) VALUES (:notification_id, :user_id, 1);";
        $stmt = $cn->prepare($sql);
        $stmt->execute(array(':notification_id' => $data['notification_id'], ':user_id' => $data['user_id']));
    }
    function updateSupplierCode($data = array()){
        $now = date('Y-m-d H:i:s');

        $cn = $this->cn;
        $sql = "UPDATE notifications set supplier_code=:supplier_code, updated_at=:updated_at, sent_at=:sent_at WHERE id = :id";
        $stmt = $cn->prepare($sql);
        $stmt->execute(array(':id' => $data['correlationId'], ':supplier_code' => $data['id'], 'updated_at' => $now, 'sent_at' => $now));
    }

    function sendNotification($postfields = array()){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-messaging.movile.com/v1/send-bulk-sms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postfields),
            CURLOPT_HTTPHEADER => array(
                "authenticationtoken: " . API_TOKEN,
                "username: " . API_USERNAME,
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $rs = array();
        if ($err) {
            $rs['error-message'] = "cURL Error #:" . $err;
        } else {
            $rs['response'] = $response;
        }
        return $rs;
    }
}