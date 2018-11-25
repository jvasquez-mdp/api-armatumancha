<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('America/Lima');

define('API_PATH', realpath(dirname(__FILE__)));
define('API_TOKEN', '53c7AHLMa9itnTTCcQszyQL5tnH92UY8M-ViCbvJ');
define('API_USERNAME', 'Claro Peru');

list($domain, $api, $action) = explode('/', $_SERVER['PATH_INFO']);

$API_KEY = (isset($_REQUEST['api-key']) ? $_REQUEST['api-key'] : null);
define('API_KEY', $API_KEY);

if(file_exists(API_PATH . "/api/{$api}.php")){
    try{
        include_once API_PATH . "/api/{$api}.php";
        $className = 'Claro' . str_replace(' ', '', ucwords(strtolower(str_replace('-', ' ', $api))));
        $objApi = new $className();

        $method = "";
        foreach(explode('-', $action) as $k => $d){
            $method.=((intval($k) === 0) ? strtolower($d) : ucfirst($d));
        }
        $objApi->{$method}();
    } catch (Exception $ex) {
        die($ex->getMessage());
    }
}