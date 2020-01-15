<?php
namespace app\controllers;

use app\models\MesseModel;
use Psr\Container\ContainerInterface;
use Slim\Views\PhpRenderer;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;

class MesseController {

    protected $consumer_key;
    protected $consumer_secret;
    protected $callback_url;
    protected $auth_url;
    protected $token_url;
    protected $info_url;
    protected $container;

    public function __construct(ContainerInterface $container) {
        session_start();
        $messe = $container['messe'];
        $this->container = $container;
        $this->consumer_key = $messe['CONSUMER_KEY'];
        $this->consumer_secret = $messe['CONSUMER_SECRET'];
        $this->callback_url = $messe['CALLBACK_URL'];
        $this->auth_url = $messe['AUTH_URL'];
        $this->token_url = $messe['TOKEN_URL'];
        $this->info_url = $messe['INFO_URL'];
    }
    public function index(Request $request, Response $response) {
        $renderer = new PhpRenderer('../app/views/messe');

        // // 特定のuser_idが存在していない場合は実行
        //  if (!isset($_SESSION['user_id'])) {

        //     // access token 発行のためのパラメータ生成
        //     $params = array(
        //         'code' => $_GET['code'],
        //         'grant_type' => 'authorization_code',
        //         'redirect_uri' => $this->callback_url,
        //         'client_id' => $this->consumer_key,
        //         'client_secret' => $this->consumer_secret,
        //     );
        
        //     // パラメータをPOST送信
        //     $options = array('http' => array(
        //         'method' => 'POST',
        //         'content' => http_build_query($params)
        //     ));
    
        //     // トークン（json）の取得
        //     $res_json = file_get_contents($this->token_url, false, stream_context_create($options));
    
        //     // phpで扱える型（配列）に変更
        //     $token = json_decode($res_json, true);
        //     if(isset($token['error'])){
        //         echo 'エラー発生';
        //         exit;
        //     }

        //     // アクセストークン / リフレッシュトークン を変数に入れる
        //     $access_token = $token['access_token'];
        //     $refresh_token = $token['refresh_token'];
            
        //     // APIリクエストパラメータにアクセストークンをセット
        //     $params = array('access_token' => $access_token);

        //     // ユーザー情報取得
        //     $res_json = file_get_contents($this->info_url . '?' . http_build_query($params));
        //     $res = json_decode($res_json, true);
        //     $user_id = $res['id'];

        //     // セッションファイルに user_id を保存
        //     $_SESSION['user_id'] = $user_id;
        //     $_SESSION['access_token'] = $access_token;
        // } else {

        //     $access_token = $_SESSION['access_token'];
        //     // APIリクエストパラメータにアクセストークンをセット
        //     $params = array('access_token' => $access_token);
        //     // ユーザー情報取得
        //     $res_json = file_get_contents($this->info_url . '?' . http_build_query($params));

        // }
        $res_json = '{"id": "1125574565545","name": "テスト太郎","given_name": "太郎","family_name": "テスト","picture": "hoge.jpg","locale": "ja"}';
        return $renderer->render($response, "app.php", ['googleUserData' => $res_json]);
    }
    public function login(Request $request, Response $response) {
        $renderer = new PhpRenderer('../app/views/messe');
        if (!isset($_SESSION['user_id'])) {
            $params = array(
                'client_id' => $this->consumer_key,
                'redirect_uri' => $this->callback_url,
                'scope' => 'https://www.googleapis.com/auth/userinfo.profile',
                'response_type' => 'code',
                'approval_prompt' => 'force',
                'access_type' => 'offline',
            );
            $link = $this->auth_url . '?' . http_build_query($params);
        } else {
            $link = 'http://localhost:8000/messe';
        }
        $viewData = [
            'slim' => [
                'link' => $link,
            ]
        ];
        return $renderer->render($response, "login.php",  $viewData);
    }
    public function ajaxLog(Request $request, Response $response) {
        $params = $request->getParsedBody();
        $date = date('Y-m-d H:i:s');

        // テキストが1文字以上かつ post データが null でない場合
        if ($params !== null && mb_strlen($params["text"], "utf-8") >= 1) {
            $params['date'] = $date;
        } else {
            $params = null;
        }
        $json_log = (new MesseModel($this->container))->ajaxLog($params);
        return $json_log;
    }
    public function observeLog (Request $request, Response $response) {
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        session_write_close();
        $messe_model = new MesseModel($this->container);
        $count = $messe_model->countLog();
        while(true) {
            sleep(3);
            $updated_count = $messe_model->countLog();
            if ($count < $updated_count) {
                $json = $messe_model->ajaxLog();
                $count = $updated_count;
                echo "data:" . $json . "\n\n";
                ob_end_flush();
                flush();
            }
        }
    }
}
