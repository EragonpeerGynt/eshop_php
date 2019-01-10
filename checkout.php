<?php

function userSet($user) {
    if(isset($user['name']) && isset($user['surname']) && isset($user['phone']) && isset($user['street']) && isset($user['postal_number'])) {
        return true;
    }
    return false;
}

function islegit($dat) {
    $errars['legit'] = 1;
    if(!isset($dat['name']) || $dat['name'] == "") {
        $errars['name'] = 1;
        $errars['legit'] = 0;
    }
    if(!isset($dat['surname']) || $dat['surname'] == "") {
        $errars['surname'] = 1;
        $errars['legit'] = 0;
    }
    if(!isset($dat['phone']) || $dat['phone'] == "") {
        $errars['phone'] = 1;
        $errars['legit'] = 0;
    }
    if(!isset($dat['street']) || $dat['street'] == "") {
        $errars['street'] = 1;
        $errars['legit'] = 0;
    }
    if(!isset($dat['postal_number']) || $dat['postal_number'] == "") {
        $errars['postal_number'] = 1;
        $errars['legit'] = 0;
    }
    return $errars;
}

function checkingBuy($user) {
    $all_data = $user;
    ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>Checkout</title>
        </head>
        <body>
            <?php
            $s_cart = isset($_SESSION["cart"]) ? $_SESSION["cart"] : [];
            if ($s_cart) {
            $total = 0;
            }
            
            foreach ($s_cart as $id => $amount):
                $book = DBBooks::getBook($id)[0];
                $total += $book['price'] * $amount;
                ?>
                <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                    <input type="hidden" name="checkout" value="checkout" />
                    <input type="hidden" name="do" value="update_cart" />
                    <input type="hidden" name="id" value="<?= $book['id_book']?>" />
                    <input type="number" name="kolicina" value="<?= $amount ?>" class="short_input" />
                    &times; <?=
                    (strlen($book['title']) < 30) ?
                            $book['title'] :
                            substr($book['title'], 0, 26) . " ..."
                    ?> 
                    <button type="submit">Update</button> 
                </form>
                <?php endforeach; ?>

                <p>Total: <b><?= number_format($total, 2) ?> EUR</b></p>
            
            <div>
                <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                    Name<br/>
                    <?php 
                    if(isset($all_data['name']) && $all_data['name'] != "") {?>
                        <input type="text" name="name" value="<?= $all_data['name'] ?>" required><br/>
                    <?php
                    }
                    else {
                    ?>
                        <input type="text" name="name" placeholder="Name" required><br/>
                    <?php    
                    }
                    ?>
                    
                    Surname<br/>
                    <?php 
                    if(isset($all_data['surname']) && $all_data['surname'] != "") {?>
                        <input type="text" name="surname" value="<?= $all_data['surname'] ?>" required><br/>
                    <?php
                    }
                    else {
                    ?>
                        <input type="text" name="surname" placeholder="Surname" required><br/>
                    <?php    
                    }
                    ?>
                        
                    Phone<br/>
                    <?php 
                    if(isset($all_data['phone'])) {?>
                        <input type="text" name="phone" value="<?= $all_data['phone'] ?>" required><br/>
                    <?php
                    }
                    else {
                    ?>
                        <input type="text" name="phone" placeholder="Phone" required><br/>
                    <?php    
                    }
                    ?>
                        
                    Address<br/>
                    <?php 
                    if(isset($all_data['street']) && $all_data['street'] != "") {?>
                        <input type="text" name="street" value="<?= $all_data['street'] ?>" required><br/>
                    <?php
                    }
                    else {
                    ?>
                        <input type="text" name="street" placeholder="Address" required><br/>
                    <?php    
                    }
                    ?>
                        
                    Post<br/>
                    <?php 
                    if(isset($all_data['postal_number'])) {
                        $kraj = DBUsers::findPost($all_data['postal_number'])[0]['town'];
                        ?>
                        <input type="text" name="postal_number" value="<?= $all_data['postal_number'] ?>" required>
                        <input type="text" name="postal_town" value="<?= $kraj ?>"<br/>
                    <?php
                    }
                    else {
                    ?>
                        <input type="text" name="postal_number" placeholder="Postal code" required>
                        <input type="text" name="postal_town" placeholder="Town"<br/>
                    <?php    
                    }
                    ?>
                    <br/>
                    <input type="hidden" name="final" value="final"/>
                    <input type="submit" name="editorial" value="Confirm"/><br/>
                </form>
            </div>
        </body>
    </html>
    <?php
}

function succEdit() {
    $iden = $_SESSION['user_id'];
    DBUsers::updateContact($iden, $_POST['name'], $_POST['surname'], $_POST['phone'], $_POST['street'], $_POST['postal_number']);
    DBOrders::submitOrder($iden, $_SESSION['cart']);
}

function gibBooks() {
    unset($_SESSION['cart']);
    DBBooks::deleteCart($_SESSION['user_id']);
    ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>Checkout</title>
        </head>
        <body>
            <div id="login">
                <div class="succ">
                    You have succesfully submited your order<br/>
                </div>
                <div class="buton">
                    <form action="./index.php" method="get">
                        <button type="submit">Continue</button>
                    </form>
                </div>
            </div>
        </body>
    </html>
    <?php
}

session_start();
$validationRules = [
    'do' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            "regexp" => "/^(add_into_cart|update_cart|purge_cart)$/"
        ]
    ],
    'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options' => ['min_range' => 0]
    ],
    'kolicina' => [
        'filter' => FILTER_VALIDATE_INT,
        'options' => ['min_range' => 0]
    ],
    'name' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-žA-Ž0-9]+$/"
        ]
    ],
    'surname' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-žA-Ž0-9]+$/"
        ]
    ],
    'phone' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[0-9]{8,9}$/"
        ]
    ],
    'street' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[A-Ža-ž0-9 ]+$/"
        ]
    ],
    'postal_number' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[0-9]{4}$/"
        ]
    ],
    'postal_town' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-žA-Ž0-9]+$/"
        ]
    ],
    'checkout' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^checkout$/"
        ]
    ],
    'final' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^final$/"
        ]
    ]
];
$_POST = filter_input_array(INPUT_POST, $validationRules);
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

if (isset($_POST['do'])) {
    if ($_POST['do'] == "add_into_cart") {
        try {
            $book = DBBooks::getBook($_POST["id"])[0];

            if (isset($_SESSION["cart"][$book['id_book']])) {
                $_SESSION["cart"][$book['id_book']] ++;
            } else {
                $_SESSION["cart"][$book['id_book']] = 1;
            }
            if ($_SESSION['user_id'] != 'guest') {
                $tmp_hash = serialize($_SESSION['cart']);
                DBBooks::updateCart($_SESSION['user_id'], $tmp_hash);
            }
        } catch (Exception $exc) {
            die($exc->getMessage());
        }
    }
    elseif ($_POST['do'] == "update_cart") {
        if (isset($_SESSION["cart"][$_POST["id"]])) {
            if ($_POST["kolicina"] > 0) {
                $_SESSION["cart"][$_POST["id"]] = $_POST["kolicina"];
            } else {
                unset($_SESSION["cart"][$_POST["id"]]);
            }
            if ($_SESSION['user_id'] != 'guest') {
                $tmp_hash = serialize($_SESSION['cart']);
                DBBooks::updateCart($_SESSION['user_id'], $tmp_hash);
            }
        }
    }
    elseif ($_POST['do'] == "purge_cart") {
        unset($_SESSION["cart"]);
        if ($_SESSION['user_id'] != 'guest') {
            DBBooks::deleteCart($_SESSION['user_id']);
        }
    }
}
//var_dump($_POST);
$user = DBUsers::getAllData($_SESSION['user_id'])[0];
if (isset($_POST['checkout']) && isset($_SESSION['cart']) && $_SESSION['cart'] != []) {
    checkingBuy($user);
}
elseif (isset ($_POST['final']) && $_POST['final'] == 'final') {
    //echo "going to final stage";
    $data = islegit($_POST);
    if ($data['legit'] == 1) {
        succEdit();
        gibBooks();
    }
    else {
        checkingBuy($user);
    }
}
elseif (!isset($_SESSION['cart']) || $_SESSION['cart'] == []) {
    $redirect = "https://" . $_SERVER['HTTP_HOST'] . "/netbeans/eshop/index.php";
    header("Location: ".$redirect);
}
