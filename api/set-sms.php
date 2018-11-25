<?php
/*
 * author   : Jonathan Vásquez
 * email    : jonathan.vasquez@mdp.com.pe
 * version  : 1.0.0
*/

include_once API_PATH . '/api/core/NotificationsModel.php';

final class ClaroSetSms{
    function run(){
        $_POST = array(
                    'data' => array(
                        'notification' => 'codigo-seguridad',
                        'users' => array(6)
                    )
                );
        if(!empty($_POST['data'])){
            $post = $_POST['data'];

            $objNotifications = new NotificationsModel();
            try{
                $objNotifications->sendTransaction($post);
            } catch (Exception $ex) {
                /* ** */
                @mkdir(API_PATH . '/logs');
                $now = date('YmdHis');
                $log_send = json_encode(array('send-sms' => $ex->getMessage()));
                $log_file = fopen(API_PATH . '/logs/' . "logs_send-{$now}.txt", "w+") or die("Unable to open file!");
                fwrite($log_file, $log_send);
                fclose($log_file);
                /* ** */
                throw new Exception($ex->getMessage());
            }
        }
    }
    function test(){
        $data = array(
            "id" => "25950050-7362-11e6-be62-001b7843e7d4",
            "subAccount" => "iFoodMarketing",
            "campaignAlias" => "iFoodPromo",
            "carrierId" => 1,
            "carrierName" => "VIVO",
            "source" => "5516981562820",
            "shortCode" => "28128",
            "messageText" => "Eu quero pizza",
            "receivedAt" => 1473088405588,
            "receivedDate" => "2016-09-05T12:13:25Z",
            "mt" => array(
              "id" => "8be584fd-2554-439b-9ba9-aab507278992",
              "correlationId" => "1876",
              "username" => "iFoodCS",
              "email" => "customer.support@ifood.com"
            )
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.armatumancha.local/get-sms/run?api-key=3bb1b6fcb4cc4f1c5f5903043b322b59",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
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
    }
}