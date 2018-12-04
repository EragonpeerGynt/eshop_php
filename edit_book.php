<?php

function overview($my_books) {
    ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>My books</title>
        </head>
        <body>
            <div id="main">
                <?php foreach ($my_books as $tmp => $knjiga): ?>
                    <div class="book">
                        <form action="<?= $_SERVER["PHP_SELF"]?>" method="get">
                            <input type="hidden" name="id_edit" value="<?= $knjiga["id_book"] ?>" />
                            <p><?= $knjiga["author_name"] ?> : <?= $knjiga["title"] ?></p>
                            <p><?= number_format($knjiga["price"], 2) ?> EUR<br/>
                            <button type="submit">EDIT</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </body>
    </html>
<?php
}

function errorReport($error) {
    ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>ERROR</title>
        </head>
        
        <body>
            <div id="login">
                <div class="error">
                    There seems to be an error
                    <?php
                    echo $error;
                    ?>
                    <br/>
                </div>
                <form action="./index.php" method="get">
                    <button type="submit">Continue</button>
                </form>
            </div>
        </body>
    </html>
    <?php
}

function editbook($book) {
    ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>Edit: <?= $book['title'] ?></title>
        </head>
        <body>
            <div id="login">
                <h1>Edit book</h1>
                <form>
                    <input type="hidden" name="id_book" value="<?= $book['id_book'] ?>"/>
                    <textarea rows="2" cols="60" name="author"><?= $book['author_name'] ?></textarea><br />
                    <textarea rows="2" cols="60" name="title"><?= $book['title'] ?></textarea><br />
                    <textarea rows="8" cols="60" name="description"><?= $book['description'] ?></textarea><br />
                    <button type="submit" name="push_book" value="old">Save</button>
                </form>
            </div>
        </body>
    </html>
    <?php
}

session_start();
//var_dump($_POST);
$url = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_SPECIAL_CHARS);
require_once 'database_knjigarna.php';
if(!isset($_SESSION['user']) || ($_SESSION['user'] == "guest" && $_SESSION['user_id'] == 'guest')) {
    session_destroy();
    ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>ERROR</title>
        </head>
        <body>
            <div id="login">
                <div class="error">
                    There seems to be an error<br/>
                </div>
                <form action="./index.php" method="get">
                    <button type="submit">Continue</button>
                </form>
            </div>
             
        </body>
    </html>
    <?php
    
}

if (isset($_POST['editorial']) && $_POST['editorial'] == $_SESSION['user_id'] && ($_SESSION['user_status'] == 'admin' || $_SESSION['user_status'] == 'seller')) {
    if ($_SESSION['user_status'] == 'admin') {
        $books = DBBooks::getAllBooks();
    }
    elseif ($_SESSION['user_status'] == 'seller') {
        $books = DBBooks::getMyBooks($_SESSION['user_id']);
    }
    overview($books);
}

elseif (isset($_GET['id_edit'])) {
    $book = DBBooks::getBook($_GET['id_edit'])[0];
    if ($_SESSION['user_status'] == 'admin' || $book['id_seller'] == $_SESSION['user_id']) {
        editbook($book);
    }
    else {
        errorReport('<br/>You are not authorized to edit<br/>'.$book['title'].' by '.$book['author_name']);
        exit();
    }
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

