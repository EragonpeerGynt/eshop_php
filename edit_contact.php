<?php

function mainpage($all_data) {
    ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>Contact data</title>
        </head>
        
            <div id="login">
                <form action="<?= $_SERVER["PHP_SELF"]?>" method="post">
                    Name<br/>
                    <?php 
                    if(isset($all_data['name']) && $all_data['name'] != "") {?>
                        <input type="text" name="name" value="<?= $all_data['name'] ?>"><br/>
                    <?php
                    }
                    else {
                    ?>
                        <input type="text" name="name" placeholder="Name"><br/>
                    <?php    
                    }
                    ?>
                    
                    Surname<br/>
                    <?php 
                    if(isset($all_data['surname']) && $all_data['surname'] != "") {?>
                        <input type="text" name="surname" value="<?= $all_data['surname'] ?>"><br/>
                    <?php
                    }
                    else {
                    ?>
                        <input type="text" name="surname" placeholder="Surname"><br/>
                    <?php    
                    }
                    ?>
                        
                    Phone<br/>
                    <?php 
                    if(isset($all_data['phone'])) {?>
                        <input type="text" name="phone" value="<?= $all_data['phone'] ?>"><br/>
                    <?php
                    }
                    else {
                    ?>
                        <input type="text" name="phone" placeholder="Phone"><br/>
                    <?php    
                    }
                    ?>
                        
                    Address<br/>
                    <?php 
                    if(isset($all_data['street']) && $all_data['street'] != "") {?>
                        <input type="text" name="street" value="<?= $all_data['street'] ?>"><br/>
                    <?php
                    }
                    else {
                    ?>
                        <input type="text" name="street" placeholder="Address"><br/>
                    <?php    
                    }
                    ?>
                        
                    Post<br/>
                    <?php 
                    if(isset($all_data['postal_number'])) {
                        $kraj = DBUsers::findPost($all_data['postal_number'])[0]['town'];
                        ?>
                        <input type="text" name="postal_number" value="<?= $all_data['postal_number'] ?>">
                        <input type="text" name="postal_town" value="<?= $kraj ?>"<br/>
                    <?php
                    }
                    else {
                    ?>
                        <input type="text" name="postal_number" placeholder="Postal code">
                        <input type="text" name="postal_town" placeholder="Town"<br/>
                    <?php    
                    }
                    ?>
                    <br/>
                      
                    <input type="submit" name="editorial" value="Confirm"/><br/>
                </form>
            </div>
            <div id="edit">
                <form action="./index.php">
                    <button type="submit">Home</button><br/>
                </form>
            </div>
        </body>
    </html>
    <?php
}

function errorEdit($errors) {
    ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>Contact data</title>
        </head>
        
            <div id="login">
                <form action="<?= $_SERVER["PHP_SELF"]?>" method="post">
                    Name<br/>
                    <?php 
                    if(isset($errors['name'])) {?>
                        <div class="error">
                            Invalid name<br/>
                        </div>  
                    <?php
                    }
                    ?>
                        <input type="text" name="name" value="<?= $_POST['name'] ?>"><br/>
                                        
                    Surname<br/>
                    <?php 
                    if(isset($errors['surname'])) {?>
                        <div class="error">
                            Invalid surname<br/>
                        </div> 
                    <?php
                    }
                    ?>
                        <input type="text" name="surname" value="<?= $_POST['surname'] ?>"><br/>
                    Phone<br/>
                    <?php 
                    if(isset($errors['phone'])) {?>
                        <div class="error">
                            Invalid phone number<br/>
                        </div> 
                    <?php
                    }
                    ?>
                        <input type="text" name="phone" value="<?= $_POST['phone'] ?>"><br/>
                    Address<br/>
                    <?php 
                    if(isset($errors['street'])) {?>
                        <div class="error">
                            Invalid street<br/>
                        </div> 
                    <?php
                    }
                    ?>
                        <input type="text" name="street" value="<?= $_POST['street'] ?>"><br/>
                    Post<br/>
                    <?php 
                    $kraj = DBUsers::findPost($_POST['postal_number'])[0]['town'];
                    if(isset($errors['postal_number'])) {
                        ?>
                        <div class="error">
                            Invalid postal number<br/>
                        </div> 
                    <?php
                    }
                    ?>
                        <input type="text" name="postal_number" value="<?= $_POST['postal_number'] ?>">
                        <input type="text" name="postal_town" value="<?= $kraj ?>"<br/>
                    <br/>    
                    
                      
                    <input type="submit" name="editorial" value="Confirm"/><br/>
                </form>
            </div>
            <div id="edit">
                <form action="./index.php">
                    <button type="submit">Home</button><br/>
                </form>
            </div>
        </body>
    </html>
    <?php
}

function succEdit() {
    if(isset($_SESSION['mockup'])) {
        $iden = $_SESSION['mockup'];
    }
    else {
        $iden = $_SESSION['user_id'];
    }
    DBUsers::updateContact($iden, $_POST['name'], $_POST['surname'], $_POST['phone'], $_POST['street'], $_POST['postal_number']);
    ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>Updated</title>
        </head>
        <body>
            <div id="login">
                <div class="succ">
                    You have succesfully updated your contact information<br/>
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

function errorReport($error) {
    ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>ERROR</title>
        </head>
        
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

function listUser() {
    if ($_SESSION['user_status'] == 'admin') {
        $operation = DBUsers::returnAllUsers();
    }
    elseif ($_SESSION['user_status'] == 'seller') {
        $operation = DBUsers::returnUsers();
    }
    
    else {
        errorReport("");
    }
    
    ?> 
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>Users</title>
        </head>
        <body>
            <div id="user">
                <?php

                foreach ($operation as $key => $uporabnik) {
                    
                    ?>
                    <div class="singleU">
                        <form action="<?= $_SERVER["PHP_SELF"]?>" method="post">
                            <input type="hidden" name="control" value="<?= $uporabnik['id_shopper'] ?>" />
                            <p><?= $uporabnik['u_name'] ?></p>
                            <button type="submit">edit</button>
                        </form>
                    </div>                     
                    <?php
                    
                }

                ?>
            </div>
        </body>    
    <?php
    
}

function islegit() {
    $errars['legit'] = 1;
    if(!isset($_POST['name']) || $_POST['name'] == "") {
        $errars['name'] = 1;
        $errars['legit'] = 0;
    }
    if(!isset($_POST['surname']) || $_POST['surname'] == "") {
        $errars['surname'] = 1;
        $errars['legit'] = 0;
    }
    if(!isset($_POST['phone']) || $_POST['phone'] == "") {
        $errars['phone'] = 1;
        $errars['legit'] = 0;
    }
    if(!isset($_POST['street']) || $_POST['street'] == "") {
        $errars['street'] = 1;
        $errars['legit'] = 0;
    }
    if(!isset($_POST['postal_number']) || $_POST['postal_number'] == "") {
        $errars['postal_number'] = 1;
        $errars['legit'] = 0;
    }
    return $errars;
}

session_start();
//var_dump($_POST);
$url = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_SPECIAL_CHARS);
require_once 'database_knjigarna.php';
if(!isset($_SESSION['user']) || ($_SESSION['user'] == "guest" && $_SESSION['user_id'] == 'guest')) {
    session_destroy();
    errorReport("");
}

elseif (isset($_POST['editorial']) && $_POST['editorial'] == "Confirm") {
    $lister = islegit();
    if($lister['legit'] == 0) {
        errorEdit($lister);
    }
    else {
        succEdit();
    }
}

elseif (isset($_POST['editorial']) && $_POST['editorial'] == "find_user") {
    listUser();
}

else {
    if(isset($_SESSION['mockup'])) {
        unset($_SESSION['mockup']);
    }
    $identification = $_SESSION['user_id'];
    if (isset($_POST['control']) && ($_SESSION['user_status'] == "admin" || $_SESSION['user_status'] == "seller")) {
        $analyse = DBUsers::getData($_POST['control'])[0];
        if ($_SESSION['user_status'] == "user" || ($_SESSION['user_status'] == "seller" && ($analyse['status'] == "admin" || $analyse['status'] == "seller"))) {
            $statement = "as " . $_SESSION['user_status'] . "<br/>you cannot edit data of equal or higher status (" . $analyse['status'] . ")";
            errorReport($statement);
            exit();
        }
        else {
            $identification = $_POST['control'];
            $_SESSION['mockup'] = $identification;
        }
    }
    $all_data = DBUsers::getAllData($identification)[0];
    mainpage($all_data);
}
