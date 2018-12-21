<?php

require_once '../database_knjigarna.php';
require_once 'ViewHelper.php';

class RESTclient {

    public static function get($id) {
        try {
            echo ViewHelper::renderJSON(DBBooks::getBook($id));
        } catch (InvalidArgumentException $e) {
            echo ViewHelper::renderJSON($e->getMessage(), 404);
        }
    }

    public static function index() {
        $prefix = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"]
                . $_SERVER["REQUEST_URI"]."/";
        echo ViewHelper::renderJSON(DBBooks::getAllBooks());
    }
    
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

