<?php
include_once API_PATH . '/api/core/DbPdo.php';

class UsersModel {
    function getUserById($id = 0){
        $objDb = new DbPdo();

        $cn = $objDb->connect();
        $sql = "SELECT * FROM users WHERE id=:id";
        $stmt = $cn->prepare($sql);
        $stmt->execute(array(':id' => $id));
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data[0];
    }
    function getDataByIds($user_ids = array()){
        $objDb = new DbPdo();

        $cn = $objDb->connect();
        $sql = "SELECT
                    u.id user_id,
                    u.alias user_alias,
                    u.numero user_number,
                    g.id group_id,
                    g.name group_name,
                    uc.code user_code
                FROM
                    group_user gu
                INNER JOIN groups g ON gu.group_id = g.id
                INNER JOIN users u ON gu.user_id = u.id
                INNER JOIN codes uc ON u.id = uc.user_id
                WHERE
                    gu.user_id IN (" . implode(',', $user_ids) . ")";
        $stmt = $cn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dataList = array();
        foreach($data as $row){
            $dataList[$row['user_id']] = $row;
        }

        return $dataList;
    }
}