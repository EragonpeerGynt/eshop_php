<?php

session_start();

require_once '../database_knjigarna.php';
require_once 'services/RESTclient.php';

define("BASE_URL", rtrim($_SERVER["SCRIPT_NAME"], "index.php"));
define("IMAGES_URL", rtrim($_SERVER["SCRIPT_NAME"], "index.php") . "static/images/");
define("CSS_URL", rtrim($_SERVER["SCRIPT_NAME"], "index.php") . "static/css/");

$path = isset($_SERVER["PATH_INFO"]) ? trim($_SERVER["PATH_INFO"], "/") : "";

$urls = [
    
    # REST API
    "/^books\/(\d+)$/" => function ($method, $id) {
        // TODO: izbris knjige z uporabo HTTP metode DELETE
        switch ($method) {
            case "PUT":
                RESTclient::edit($id);
                break;
            default: # GET
                RESTclient::get($id);
                break;
        }
    },
    "/^books$/" => function ($method) {
        switch ($method) {
            case "POST":
                RESTclient::add();
                break;
            case "DELETE":
                BookDB::delete($id);
                break;
            default: # GET
                RESTclient::index();
                break;
        }
    },
];
    

foreach ($urls as $pattern => $controller) {
    if (preg_match($pattern, $path, $params)) {
        try {
            $params[0] = $_SERVER["REQUEST_METHOD"];
            $controller(...$params);
        } catch (InvalidArgumentException $e) {
            ViewHelper::error404();
        } catch (Exception $e) {
            ViewHelper::displayError($e, true);
        }

        exit();
    }
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

