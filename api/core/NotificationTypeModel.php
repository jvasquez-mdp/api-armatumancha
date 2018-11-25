<?php
include_once API_PATH . '/api/core/DbPdo.php';
include_once API_PATH . '/api/core/UsersModel.php';

class NotificationTypeModel {
    function getNotificationTypeByAlias($alias = ""){
        $objDb = new DbPdo();
        
        $cn = $objDb->connect();
        $sql = "SELECT * FROM notification_type WHERE alias=:alias";
        $stmt = $cn->prepare($sql);
        $stmt->execute(array(':alias' => $alias));
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data[0];
    }
    function getContentPro($content = "", $data = array()){
        $content = str_replace(
                        array(
                            '[USER_CODE]',
                            '[USER_ALIAS]',
                            '[GROUP_NAME]',
                            /*'[START_DATE]',
                            '[END_DATE]',
                            '[USERS_TOTAL]',
                            '[GB_NUMBER]',
                            '[PERCENTAGE_NUMBER]'*/
                        ), 
                        array(
                            $data['user_code'],
                            $data['user_alias'],
                            $data['group_name'],
                            /*$data['start_date'],
                            $data['end_date'],
                            $data['users_total'],
                            $data['gb_number'],
                            $data['percentage_number']*/
                        ),
                        $content);
        return $content;
    }
}