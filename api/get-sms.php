<?php
/*
 * author   : Jonathan Vásquez
 * email    : jonathan.vasquez@mdp.com.pe
 * version  : 1.0.0
*/
include_once API_PATH . '/api/core/NotificationResponseModel.php';

final class ClaroGetSms{
    function run(){
        $api_key = API_KEY;
        $mo_data = json_decode(file_get_contents('php://input'), true);
        
        $authorized = false;
        if($api_key === '3bb1b6fcb4cc4f1c5f5903043b322b59'){
            $response["message"] = array('success' => array('text' => 'awesome!!'));
            $authorized = true;
        }else if($api_key === null){
            $response["error"] = true;
            $response["message"] = array('error' => array('text' => 'api-key not sent!!'));
            $authorized = false;
        }else{
            $response["error"] = true;
            $response["message"] = array('error' => array('text' => 'api-key invalid!!'));
            $authorized = false;
        }
        if($authorized){
            $objNotificationResponse = new NotificationResponseModel();
            if($mo_data !== null){
                if(!empty($mo_data)){
                    try{
                        $objNotificationResponse->responseTransaction($mo_data);
                    } catch (Exception $ex) {
                        /* ** */
                        @mkdir(API_PATH . '/logs');
                        $now = date('YmdHis');
                        $log_send = json_encode(array('get-sms' => $ex->getMessage()));
                        $log_file = fopen(API_PATH . '/logs/' . "logs_get-sms-{$now}.txt", "w+") or die("Unable to open file!");
                        fwrite($log_file, $log_send);
                        fclose($log_file);
                        /* ** */
                        throw new Exception($ex->getMessage());
                    }
                }
            }else{
                $response["error"] = true;
                $response["message"] = array('error' => array('text' => 'mo-data undefined!!'));
                $authorized = false;
            }
        }
        echo json_encode($response);
    }
}