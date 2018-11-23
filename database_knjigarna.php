<?php

require_once 'database_init.php';

class DBBooks {

    public static function getAllBooks() {
        $db = DBInit::getInstance();

        $statement = $db->prepare("SELECT library.id_book, library.title, library.description, library.price, author.author_name FROM library INNER JOIN author ON author.id_author = library.id_author");
        $statement->execute();

        return $statement->fetchAll();
    }
    
    public static function getBook($id) {
        
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT library.id_book, library.title, library.description, library.price, author.author_name FROM library INNER JOIN author ON author.id_author = library.id_author WHERE library.id_book = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();

        return $statement->fetchAll();
        
    }
    
    public static function loadCart($id) {
        
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT hasher FROM cart WHERE id_buyer = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();
        
        return $statement->fetchAll();
        
    }
    
    public static function updateCart($id, $c_hash) {
        
        $db = DBInit::getInstance();
        $statement = $db->prepare("DELETE FROM cart WHERE id_buyer = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();
        
        $db2 = DBInit::getInstance();
        $statement = $db2->prepare("INSERT INTO cart (id_buyer, hasher) VALUES (:id, :c_hash)");
        $statement->bindParam(":id", $id);
        $statement->bindParam(":c_hash", $c_hash);
        $statement->execute();
        
    }

    public static function deleteCart($id) {
        
        $db = DBInit::getInstance();
        $statement = $db->prepare("DELETE FROM cart WHERE id_buyer = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();
        
    }
    
}

class DBUsers {
    
    public static function findUser($name) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT u_name, email FROM user WHERE u_name = :name");
        $statement->bindParam(":name", $name);
        $statement->execute();

        return $statement->fetchAll();
    }
    
    public static function fetchPass($name, $pass) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT id_shopper, status, hash FROM user WHERE u_name = :name AND u_pass = :pass");
        $hasher = DBUsers::findUser($name)[0];
        $salt = $hasher['u_name'] . $hasher['email'];
        $passwd_hash = crypt($pass, $salt);
        $statement->bindParam(":name", $name);
        $statement->bindParam(":pass", $passwd_hash);
        try {
            $statement->execute();
        }
        catch (Exception $exc) {
            return [];
        }

        return $statement->fetchAll();
    }
    
    public static function registerUser($name, $passwd, $mail) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("INSERT INTO user (u_name, u_pass, email, status) VALUES (:u_name, :u_pass, :mail, 'user')");
        $statement->bindParam(":u_name", $name);
        $salt = $name . $mail;
        $passwd_hash = crypt($passwd, $salt);
        $statement->bindParam(":u_pass", $passwd_hash);
        $statement->bindParam(":mail", $mail);
        $statement->execute();
    }
    
    public static function getData($id) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT u_name, email, hash, status FROM user WHERE id_shopper = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();
        
        return $statement->fetchAll();
    }
    
    public static function secureConnect($id, $user, $pass) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT id_shopper FROM user WHERE id_shopper = :id AND u_pass = :pass AND u_name = :name");
        //u_name = :name AND u_pass = :pass AND 
        $statement->bindParam(":name", $user);
        $statement->bindParam(":pass", $pass);
        $statement->bindParam(":id", $id);
        $statement->execute();

        return $statement->fetchAll();
    }
    
    public static function updateAtribute($id, $value, $attribute) {
        $db = DBInit::getInstance();
        //$statement = $db->prepare("SELECT id_shopper FROM user WHERE u_name = :name AND u_pass = :pass AND id_shopper = :id");
        if ($attribute == "username") {
            $statement = $db->prepare("UPDATE user SET u_name = :name WHERE id_shopper = :id");
            $statement->bindParam(":name", $value);
        }
        else if ($attribute == "passwd") {
            $statement = $db->prepare("UPDATE user SET u_pass = :pass WHERE id_shopper = :id");
            $statement->bindParam(":pass", $value);
        }
        else if ($attribute == "") {
            $statement = $db->prepare("UPDATE user SET email = :mail WHERE id_shopper = :id");
            $statement->bindParam(":mail", $value);
        }
        else {
            return;
        }
        $statement->bindParam(":id", $id);
        $statement->execute();
    }

    /*public static function delete($id) {
        $db = DBInit::getInstance();

        $statement = $db->prepare("DELETE FROM jokes WHERE id = :id");
        $statement->bindParam(":id", $id, PDO::PARAM_INT);
        $statement->execute();
    }

    public static function get($id) {
        $db = DBInit::getInstance();

        $statement = $db->prepare("SELECT id, joke_text, joke_date FROM jokes 
            WHERE id =:id");
        $statement->bindParam(":id", $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public static function insert($joke_date, $joke_text) {
        $db = DBInit::getInstance();

        $statement = $db->prepare("INSERT INTO jokes (joke_date, joke_text)
            VALUES (:joke_date, :joke_text)");
        $statement->bindParam(":joke_date", $joke_date);
        $statement->bindParam(":joke_text", $joke_text);
        $statement->execute();
    }

    public static function update($id, $joke_date, $joke_text) {
        $db = DBInit::getInstance();

        $statement = $db->prepare("UPDATE jokes SET joke_date = :joke_date,
            joke_text = :joke_text WHERE id =:id");
        $statement->bindParam(":joke_date", $joke_date);
        $statement->bindParam(":joke_text", $joke_text);
        $statement->bindParam(":id", $id, PDO::PARAM_INT);
        $statement->execute();
    }*/

}

