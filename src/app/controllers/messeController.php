<?php
namespace app\controllers;

use app\models\MesseModel;
use app\models\GoogleAuthModel;
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
    protected $exchange_token_url;
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
        $this->exchange_token_url = $messe['EXCHANGE_TOKEN_URL'];
        $this->info_url = $messe['INFO_URL'];
    }

    public function index(Request $request, Response $response) {
        // session_id ハイジャック対策
        session_regenerate_id(true);
        $renderer = new PhpRenderer('../app/views/messe');
        $googleAuth = new GoogleAuthModel();

        // GUEST ユーザー処理
        if ($_GET['guest'] && !isset($_SESSION['guest_user'])) {
            $user_info = '{"id": "'.  uniqid() .'","name": "GUEST USER","given_name": "GUEST","family_name": "", "picture": "", "locale": "ja"}';
            $_SESSION['guest_user'] = $user_info;
            return $renderer->render($response, "app.php", ['googleUserData' => $user_info]);
        } else if (
            isset($_SESSION['guest_user']) &&
            !isset($_SESSION['user']) &&
            !isset($_GET['code'])
        ) {
            return $renderer->render($response, "app.php", ['googleUserData' => $_SESSION['guest_user']]);
        }

        // 特定のuser_idが存在していない場合は実行
        if (!isset($_SESSION['user'])) {
            // access token 発行のためのパラメータ生成
            $params = array(
                'code' => $_GET['code'],
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->callback_url,
                'client_id' => $this->consumer_key,
                'client_secret' => $this->consumer_secret,
            );

            // Invalid $_GET['code']: return code 400($token = false)
            $token = $googleAuth->getToken($params, $this->token_url);

            if ($token) {
                $access_token = $token['access_token'];
                $refresh_token = $token['refresh_token'];

                // ユーザー情報取得
                $params = array('access_token' => $access_token);
                $user_info = $googleAuth->getUserInfo($params, $this->info_url, false);

                $user = [
                    'user_id' => json_decode($user_info, true)['id'],
                    'a_token' => $access_token,
                    'r_token' => $refresh_token,
                    'limit' => date('Y-m-d H:i:s')
                ];
                // セッションファイルに user 情報を保存
                $_SESSION['user'] = json_encode($user, JSON_UNESCAPED_UNICODE);
            } else {
                return $response->withRedirect('/messe/login');
            }
        } else {
            $user = json_decode($_SESSION['user'], true);
            $registration_date = strtotime($user['limit']); 
            $today = strtotime(date('Y-m-d H:i:s'));

            if ($today - $Registration_date >= 3600) {
                $refresh_token = $user['r_token'];
                $params = array(
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refresh_token,
                    'client_id' => $this->consumer_key,
                    'client_secret' => $this->consumer_secret,
                );
                
                // Invalid refrash_token: return code 400($token = false)
                $token = $googleAuth->getToken($params, $this->exchange_token_url);

                if ($token) {
                    $access_token = $token['access_token'];
                    $updating_user_data = [
                        'user_id' => $user['user_id'],
                        'a_token' => $access_token,
                        'r_token' => $user['r_token'],
                        'limit' => date('Y-m-d H:i:s')
                    ];
                    // セッションファイルに user 情報を保存
                    $user = $updating_user_data;
                    $_SESSION['user'] = json_encode($updating_user_data, JSON_UNESCAPED_UNICODE);
                } else {
                    $_SESSION['user'] = null;
                    return $response->withRedirect('/messe/login');
                }
            }

            $params = array('access_token' => $user['a_token']);

            // Invalid acsess_token: return code 401($user_info = false)
            $user_info = $googleAuth->getUserInfo($params, $this->info_url, false);
        }
        return $renderer->render($response, "app.php", ['googleUserData' => $user_info]);
    }

    public function login(Request $request, Response $response) {
        $renderer = new PhpRenderer('../app/views/messe');
        // ゲストユーザー処理
        if (isset($_SESSION['guest_user'])) {
            return $response->withRedirect('/messe');
        }
        // Google ログインユーザ処理
        if (!isset($_SESSION['user'])) {
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
            return $response->withRedirect('/messe');
        }
        $viewData = [
            'slim' => [
                'link' => $link,
            ]
        ];
        return $renderer->render($response, "login.php",  $viewData);
    }

    public function logout(Request $request, Response $response) {
        // ゲストユーザー処理：Google ログインユーザーが以前ゲストでログインした情報を持っている場合を考慮
        if (isset($_SESSION['guest_user']) && !isset($_SESSION['user'])) {
            $_SESSION['guest_user'] = null;
            return $response->withRedirect('/messe/login');
        }
        $user = json_decode($_SESSION['user'], true);
        $googleAuth = new GoogleAuthModel();
        $res = $googleAuth->logout($user['a_token']);
        $_SESSION['user'] = null;
        // 失敗した場合の識別として logout=0 のクエリを付与。擬似的にログアウトする
        $redirect_url = ($res) ? '/messe/login' : '/messe/login?logout=0';
        return $response->withRedirect($redirect_url);
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
            sleep(2);
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
