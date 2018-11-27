<?php

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

session_start();
//var_dump($_POST);
$url = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_SPECIAL_CHARS);
require_once 'database_knjigarna.php';
if(!isset($_SESSION['user']) || ($_SESSION['user'] == "guest" && $_SESSION['user_id'] == 'guest')) {
    session_destroy();
    errorReport("");
}

else {
    $identification = $_SESSION['user_id'];
    if (isset($_POST['control']) && ($_SESSION['status'] == "admin" || $_SESSION['status'] == "seller")) {
        $analyse = DBUsers::getData($_POST['control'])[0];
        if ($analyse['status'] == "user" || ($_SESSION['status'] == "seller" && ($analyse['status'] == "admin" || $analyse['status'] == "seller"))) {
            $statement = "as " . $_SESSION['status'] . "<br/>you cannot edit data of equal or higher status (" . $analyse['status'] . ")";
            errorReport($statement);
            exit();
        }
        else {
            $identification = $_POST['control'];
        }
    }
    $all_data = DBUsers::getAllData($identification)[0];
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
                        
                </form>
            </div>
             
        </body>
    </html>
    <?php
}
