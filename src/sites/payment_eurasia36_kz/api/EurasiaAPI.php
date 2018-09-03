<?php

require_once __DIR__ . '/../settings.php'; # settings needs to be in a separate file due to requirements of portability

class EurasiaAPI {
    
    
    /**
     * Проверка доступности вебсервиса
     *
     * @return array
     */
    public static function ping() {
        $url = 'insurance/check/ping';
        
        $return = self::request($url, '{}', 'get');
        
        return json_decode($return, true);
    }
    
    public static function getPersonByIIN($iin) {
        
        $data = [
            'idNumber' => str_replace(" ", '', $iin)
        ];
        
        $data = json_encode($data);
        
        $url = 'insurance/policy/fetch-driver/';
        
        return self::request($url, $data);
    }
    
    /**
     * Запрос к API
     *
     * @param string $url адрес АПИ метода
     * @param object $data данные
     */
    public static function request($url, $data, $method = 'post', $apiLang = 'ru') {
        
        global $PARAM_WS_HOST, $PARAM_WS_USER, $PARAM_WS_PWD;
        
        $curl = curl_init();
        
        // WS под паролем
        curl_setopt($curl, CURLOPT_USERPWD, $PARAM_WS_USER . ":" . $PARAM_WS_PWD);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, 'grafica-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $PARAM_WS_HOST.$url);
        
        if($method == 'post') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json', 'Accept-language: '.$apiLang]);
        } else {
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept: application/json, text/plain', 'Accept-language: '.$apiLang]);
        }
        
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        //curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        //curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        
        $out = curl_exec($curl); # Initiate a request to the API and stores the response variable
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        // ошибка вызова
        if($code != 200) {
            
            // запишем ошибку в лог
            error_log("Code $code, message $out, URL $url, data $data");
            
            return json_encode(['error' => true, 'code' => $code, 'message' => json_decode($out, true)]);
        } else {
            // всё ок, вернем результат
            return $out;
        }
    }
}