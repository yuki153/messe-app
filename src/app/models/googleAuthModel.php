<?php
namespace app\models;

class GoogleAuthModel {

    public function __construct() {
    }
    /**
     * @param array $params
     * @param string $token_url
     * @return (array|boolean)
     */
    public function getToken($params, $token_url) {
        // パラメータをPOST送信
        $options = array('http' => array(
            'method' => 'POST',
            'content' => http_build_query($params)
        ));

        // トークンの取得
        $res = @file_get_contents($token_url, false, stream_context_create($options));
        $token = json_decode($res, true);
        if(isset($token['error'])){
            echo 'エラー発生';
            exit;
        }
        return ($res !== false) ? $token : $res;
    }
    /**
     * @param array $params
     * @param string $info_url
     * @param boolean $id_decode
     * @return (array|string|boolean)
     */
    public function getUserInfo($params, $info_url, $is_decode) {
        $res = @file_get_contents($info_url . '?' . http_build_query($params));
        if ($res !== false) {
            $result = ($is_decode) ? json_decode($res, true) : $res;
        } else {
            $result = $res;
        }
        return $result;
    }

    /**
     * memo1: https://github.com/googleapis/google-api-dotnet-client/issues/1285
     * memo2: https://github.com/googleapis/google-cloud-common/issues/260#issuecomment-413231697
     * memo3: https://developers.google.com/identity/protocols/OAuth2WebServer#tokenrevoke
     * @param string $token: acsess_token
     */
    public function logout($token) {
        $ch = curl_init('https://oauth2.googleapis.com/revoke?token=' . $token);
        curl_setopt($ch,CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_exec($ch);
        $res = true;
        if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
            case 200:
                break;
            default:
                $res = false;
            }
        }
        curl_close($ch);
        return $res;
    }
}
