<?php
/*
 * author   : Jonathan Vásquez
 * email    : jonathan.vasquez@mdp.com.pe
 * version  : 1.0.0
*/

include_once API_PATH . '/api/core/SentStatus.php';
include_once API_PATH . '/api/core/DeliveryReport.php';

final class ClaroCallback{
    function sentStatus(){ //estados de envio de la operadora
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $postData = json_decode(file_get_contents('php://input'), true);

            $data['supplier_code'] = @$postData['id'];
            $data['notification_id'] = @$postData['correlationId'];
            $data['carrier_id'] = @$postData['carrierId'];
            $data['carrier_name'] = @$postData['carrierName'];
            $data['user_number'] = @$postData['destination'];
            $data['sent_status_id'] = @$postData['sentStatusCode'];
            $data['sent_status'] = @$postData['sentStatus'];
            $data['sent_at'] = @date('H:i:s', intval($postData['sentAt'])/1000);
            $data['sent_date'] = @date('Y-m-d H:i:s', strtotime($postData['sentDate']));
            $data['campaign_id'] = @$postData['campaignId'];
            $data['extra_info'] = @$postData['extraInfo'];

            $objSentStatus = new SentStatus();
            try{
                $objSentStatus->callbackTransaction($data);
            } catch (Exception $ex) {
                /* ** */
                @mkdir(API_PATH . '/logs');
                $now = date('YmdHis');
                $log_send = json_encode(array('callback-sms' => $ex->getMessage()));
                $log_file = fopen(API_PATH . '/logs/' . "logs_sent-status-{$now}.txt", "w+") or die("Unable to open file!");
                fwrite($log_file, $log_send);
                fclose($log_file);
                /* ** */
                throw new Exception($ex->getMessage());
            }
        }
    }
    function deliveryReport(){ //reporte de notificaciones recibidas del usuario
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $postData = json_decode(file_get_contents('php://input'), true);

            $data['supplier_cod'] = @$postData['id'];
            $data['notification_id'] = @$postData['correlationId'];
            $data['carrier_id'] = @$postData['carrierId'];
            $data['carrier_name'] = @$postData['carrierName'];
            $data['user_number'] = @$postData['destination'];
            $data['sent_status_id'] = @$postData['sentStatusCode'];
            $data['sent_status'] = @$postData['sentStatus'];
            $data['sent_status_at'] = @date('H:i:s', intval($postData['sentStatusAt'])/1000);
            $data['sent_status_date'] = @date('Y-m-d H:i:s', strtotime($postData['sentStatusDate']));
            $data['delivered_status_id'] = @$postData['deliveredStatusCode'];
            $data['delivered_status'] = @$postData['deliveredStatus'];
            $data['delivered_at'] = @date('H:i:s', intval($postData['deliveredAt'])/1000);
            $data['delivered_date'] = @date('Y-m-d H:i:s', strtotime($postData['deliveredDate']));
            $data['campaign_id'] = @$postData['campaignId'];

            $objDeliveryReport = new DeliveryReport();
            try{
                $objDeliveryReport->callbackTransaction($data);
            } catch (Exception $ex) {
                /* ** */
                @mkdir(API_PATH . '/logs');
                $now = date('YmdHis');
                $log_send = json_encode(array('callback-sms' => $ex->getMessage()));
                $log_file = fopen(API_PATH . '/logs/' . "logs_delivery-report-{$now}.txt", "w+") or die("Unable to open file!");
                fwrite($log_file, $log_send);
                fclose($log_file);
                /* ** */
                throw new Exception($ex->getMessage());
            }
        }
    }
}