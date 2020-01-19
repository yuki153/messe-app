<?php
namespace app\models;

use Psr\Container\ContainerInterface;
use PDO;

class MesseModel {

    protected $db;

    public function __construct(ContainerInterface $container) {
        $messe = $container['messe'];
        $HOST = 'mysql:host=' . $messe['DB_HOST'] . ';';
        $DB_NAME = 'dbname=' . $messe['DB_DATABASE'] . ';';
        $CHARSET = 'charset=utf8mb4';
        $USER_NAME = $messe['DB_USERNAME'];
        $PASS = $messe['DB_PASSWORD'];
        try {
            $this->db = new PDO($HOST . $DB_NAME . $CHARSET, $USER_NAME, $PASS);
            //エラーをスロー
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //fetch 設定
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * @param $params
     */
    public function ajaxLog($params = null) {
        $get_query = 'SELECT * FROM chat_logs';
        $delete_query = 'DELETE FROM chat_logs';
        $insert_query = <<<EOL
            INSERT INTO
                `chat_logs`
            VALUES
                (
                    '{$params['user_id']}',
                    '{$params['user_name']}',
                    '{$params['text']}',
                    '{$params['img_url']}',
                    '{$params['date']}'
                )
EOL;
        // -- debug --
        // $this->db->query($delete_query);

        if ($params !== null) $dbData = $this->db->query($insert_query);
    
        // SQLステートメントを実行し、結果を変数に格納
        $dbData = $this->db->query($get_query);
        //Array化
        $dbData_array = $dbData->fetchAll();
        //json化
        $dbData_json = json_encode($dbData_array, JSON_UNESCAPED_SLASHES);
        return $dbData_json;
    }

    public function countLog($id) {
        // memo: https://oracle.tf17.net/plsql/?p=258
        $query = 'SELECT COUNT(*) FROM chat_logs WHERE user_id not in ("' . $id . '")';
        $result = $this->db->query($query);
        $count = $result->fetch(PDO::FETCH_NUM)[0];
        return $count;
    }
}
