<?php

require_once 'database_init.php';

class DBBooks {

    public static function getAllBooks() {
        $db = DBInit::getInstance();

        $statement = $db->prepare("SELECT library.id_book, library.title, library.description, library.price, author.author_name, library.hidden FROM library INNER JOIN author ON author.id_author = library.id_author ORDER BY library.id_book ASC");
        $statement->execute();

        return $statement->fetchAll();
    }
    
    public static function getAllBooksURI($prefix) {
        $db = DBInit::getInstance();

        $statement = $db->prepare("SELECT library.id_book AS id, author.author_name AS author, library.title AS title, library.price AS price, 2000 AS year, CONCAT(:prefix, library.id_book) AS uri FROM library INNER JOIN author ON author.id_author = library.id_author ORDER BY library.id_book ASC");
        $statement->bindParam(":prefix", $prefix);
        $statement->execute();

        return $statement->fetchAll();
    }

        public static function getMyBooks($id) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT library.id_book, library.title, library.description, library.price, author.author_name FROM library INNER JOIN author ON author.id_author = library.id_author WHERE library.id_seller = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();

        return $statement->fetchAll();
    }
    
    public static function getBook($id) {
        
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT library.id_book, library.title, library.description, library.price, author.author_name, library.id_seller, library.hidden FROM library INNER JOIN author ON author.id_author = library.id_author WHERE library.id_book = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();

        return $statement->fetchAll();
        
    }
    
    
    
    public static function getBookwithURI($id) {
        
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT library.id_book AS id, author.author_name AS author, library.title AS title, library.description AS description, library.price AS price, 2000 AS year FROM library INNER JOIN author ON author.id_author = library.id_author WHERE library.id_book = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();

        return $statement->fetchAll()[0];
        
    }
    
    public static function delBook($id) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("DELETE FROM library WHERE id_book = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();
    }
    
    public static function updateBook($id, $title, $author, $description, $price, $hidden) {
        $id_auth = DBBooks::getAuthor($author);
        if (count($id_auth) == 0) {
            $id_auth = DBBooks::generateAuthor($author);
        }
        $db = DBInit::getInstance();
        $statement = $db->prepare("UPDATE library SET title = :title, description = :description, price = :price, id_author = :id_author, hidden = :hidden WHERE id_book = :book");
        $statement->bindParam(":title", $title);
        $statement->bindParam(":description", $description);
        $statement->bindParam(":price", $price);
        $statement->bindParam(":id_author", $id_auth[0]['id_author']);
        $statement->bindParam(":hidden", $hidden);
        $statement->bindParam(":book", $id);
        $statement->execute();
    }
    
    public static function createBook($title, $author, $description, $price, $seller, $hidden) {
        $id_auth = DBBooks::getAuthor($author);
        if (count($id_auth) == 0) {
            $id_auth = DBBooks::generateAuthor($author);
        }
        $db = DBInit::getInstance();
        $statement = $db->prepare("INSERT INTO library (title, description, price, id_author, id_seller, hidden) VALUES (:title, :description, :price, :id_author, :id_seller, :hidden)");
        $statement->bindParam(":title", $title);
        $statement->bindParam(":description", $description);
        $statement->bindParam(":price", $price);
        $statement->bindParam(":id_author", $id_auth[0]['id_author']);
        $statement->bindParam(":id_seller", $seller);
        $statement->bindParam(":hidden", $hidden);
        $statement->execute();
    }
    
    public static function deleteBook($id) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("DELETE FROM ord_items WHERE id_book = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();
        $db = DBInit::getInstance();
        $statement = $db->prepare("DELETE FROM library WHERE id_book = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();
    }
    
    public static function generateAuthor($name) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("INSERT INTO author (author_name) VALUES (:name)");
        $statement->bindParam(":name", $name);
        $statement->execute();
        
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT id_author FROM author WHERE author_name = :name");
        $statement->bindParam(":name", $name);
        $statement->execute();

        return $statement->fetchAll();
    }
    
    public static function getAuthor($name) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT id_author FROM author WHERE author_name = :name");
        $statement->bindParam(":name", $name);
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
        $hasher = DBUsers::getData($id)[0];
        $salt = $hasher['u_name'] . $hasher['email'];
        $passwd_hash = crypt($pass, $salt);
        $statement->bindParam(":pass", $passwd_hash);
        $statement->bindParam(":id", $id);
        $statement->execute();

        return $statement->fetchAll();
    }
    
    public static function updateAtribute($id, $value, $attribute, $pass) {
        $db = DBInit::getInstance();
        //$statement = $db->prepare("SELECT id_shopper FROM user WHERE u_name = :name AND u_pass = :pass AND id_shopper = :id");
        $data = DBUsers::getData($id)[0];
        $salt = $data['u_name'] . $data['email'];
        $passwd = $pass;
        if ($attribute == "username") {
            $statement = $db->prepare("UPDATE user SET u_name = :name, u_pass = :pass WHERE id_shopper = :id");
            $statement->bindParam(":name", $value);
            $salt = $value . $data['email'];
        }
        else if ($attribute == "passwd") {
            $statement = $db->prepare("UPDATE user SET u_pass = :pass WHERE id_shopper = :id");
            $passwd = $value;
        }
        else if ($attribute == "") {
            $statement = $db->prepare("UPDATE user SET email = :mail, u_pass = :pass WHERE id_shopper = :id");
            $statement->bindParam(":mail", $value);
            $salt = $data['name'] . $value;
        }
        else {
            return;
        }
        $statement->bindParam(":id", $id);
        $passwd_hash = crypt($passwd, $salt);
        $statement->bindParam(":pass", $passwd_hash);
        
        $statement->execute();
    }
    
    public static function updateAllAtributes($id, $uname, $email, $pass) {
        $salt = $uname . $email;
        $passwd = $pass;
        $passwd_hash = crypt($passwd, $salt);
        
        $db = DBInit::getInstance();
        $statement = $db->prepare("UPDATE user SET u_name = :name, email = :email, u_pass = :pass WHERE id_shopper = :id");
        $statement->bindParam(":name", $uname);
        $statement->bindParam(":email", $email);
        $statement->bindParam(":pass", $passwd_hash);
        $statement->bindParam(":id", $id);
        
        $statement->execute();
        
    }

    public static function getAllData($id) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT * FROM user WHERE id_shopper = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();
        
        return $statement->fetchAll();
    }
    
    public static function findPost($number) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT * FROM postal WHERE postal_number = :number");
        $statement->bindParam(":number", $number);
        $statement->execute();
        
        return $statement->fetchAll();
    }
    
    public static function updateContact($id, $name, $surname, $phone, $street, $postal_number) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("UPDATE user SET name = :name, surname = :surname, phone = :phone, street = :street, postal_number = :postal_number WHERE id_shopper = :id");
        $statement->bindParam(":id", $id);
        $statement->bindParam(":name",$name);
        $statement->bindParam(":surname",$surname);
        $statement->bindParam(":phone",$phone);
        $statement->bindParam(":street",$street);
        $statement->bindParam(":postal_number",$postal_number);
        $statement->execute();
    }

    public static function returnUsers() {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT u_name, id_shopper FROM user WHERE status = 'user'");
        $statement->execute();
        
        return $statement->fetchAll();
    }
    
    public static function returnAllUsers() {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT u_name, id_shopper FROM user WHERE status = 'user' OR status = 'seller'");
        $statement->execute();
        
        return $statement->fetchAll();
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

class DBOrders {
    public static function submitOrder($id_shopper, $cart) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("INSERT INTO orders (id_buyer) VALUES (:id_buyer)");
        $statement->bindParam(":id_buyer", $id_shopper);
        $statement->execute();
        
        $id_order = $db->lastInsertId();
        foreach ($cart as $bukva=>$quantity) {
            DBOrders::fractionOrder($id_order, $bukva, $quantity);
        }
    }
    
    public static function fractionOrder($id_order, $bukva, $quantity) {
        $id_seller = DBBooks::getBook($bukva)[0]['id_seller'];
        $db = DBInit::getInstance();
        $statement = $db->prepare("INSERT INTO ord_items (id_order, id_book, quantity) VALUES (:id_order, :id_book, :quantity)");
        $statement->bindParam(":id_order", $id_order);
        $statement->bindParam(":id_book", $bukva);
        $statement->bindParam(":quantity", $quantity);
        $statement->execute();
    }
    
    public static function getHistory($id) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT orders.id_order, library.id_book, library.title, author.author_name, ord_items.quantity, library.price, ord_items.status, orders.canceled FROM orders INNER JOIN ord_items on orders.id_order = ord_items.id_order INNER JOIN library ON ord_items.id_book = library.id_book INNER JOIN author ON author.id_author = library.id_author WHERE orders.id_buyer = :id ORDER BY orders.canceled, orders.id_order, library.id_book ASC");
        $statement->bindParam(":id", $id);
        $statement->execute();
        
        return $statement->fetchAll();
    }
    
    public static function getSingleHistory($id, $id_o) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT orders.id_order, library.id_book, library.title, author.author_name, ord_items.quantity, library.price, ord_items.status, orders.canceled FROM orders INNER JOIN ord_items on orders.id_order = ord_items.id_order INNER JOIN library ON ord_items.id_book = library.id_book INNER JOIN author ON author.id_author = library.id_author WHERE orders.id_buyer = :id AND ord_items.id_order = :id_o ORDER BY orders.id_order, library.id_book ASC");
        $statement->bindParam(":id", $id);
        $statement->bindParam(":id_o", $id_o);
        $statement->execute();
        
        return $statement->fetchAll();
    }
    
    public static function cancelOrder($id) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("UPDATE orders SET canceled = 1 WHERE id_order = :id");
        $statement->bindParam(":id", $id);
        $statement->execute();
    }
    
    public static function getPending($id) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT orders.id_order, library.id_book, library.title, author.author_name, ord_items.quantity, library.price, ord_items.status, orders.canceled FROM orders INNER JOIN ord_items ON orders.id_order = ord_items.id_order INNER JOIN library ON ord_items.id_book = library.id_book INNER JOIN author ON author.id_author = library.id_author WHERE library.id_seller = :id ORDER BY orders.canceled, ord_items.status, orders.id_order, library.id_book ASC");
        $statement->bindParam(":id", $id);
        $statement->execute();
        
        return $statement->fetchAll();
    }
    
    public static function orderFinished($id_order, $id_seller) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("UPDATE ord_items INNER JOIN library ON library.id_book = ord_items.id_book SET ord_items.status = 1 WHERE ord_items.id_order = :id_order AND library.id_seller = :id_seller");
        $statement->bindParam(":id_order", $id_order);
        $statement->bindParam(":id_seller", $id_seller);
        $statement->execute();
    }
    
    public static function orderDeny($id_order, $id_seller) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("UPDATE ord_items INNER JOIN library ON library.id_book = ord_items.id_book SET ord_items.status = 2 WHERE ord_items.id_order = :id_order AND library.id_seller = :id_seller");
        $statement->bindParam(":id_order", $id_order);
        $statement->bindParam(":id_seller", $id_seller);
        $statement->execute();
    }
    
    public static function currStatus($id_order) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT canceled FROM orders WHERE id_order = :id");
        $statement->bindParam(":id", $id_order);
        $statement->execute();
        
        return $statement->fetchAll();
    }
    
    public static function doingMyPart($id_seller, $id_order) {
        $db = DBInit::getInstance();
        $statement = $db->prepare("SELECT * FROM ord_items INNER JOIN library ON ord_items.id_book = library.id_book WHERE ord_items.id_order = :id_order AND library.id_seller = :id_seller");
        $statement->bindParam(":id_order", $id_order);
        $statement->bindParam(":id_seller", $id_seller);
        $statement->execute();
        
        return $statement->fetchAll();
    }
}

