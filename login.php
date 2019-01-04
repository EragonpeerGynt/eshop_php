<?php
function legit() {
    if (!isset($_POST["u_name"]) || $_POST["u_name"] == "") {
        return FALSE;
    }
    if (!isset($_POST["passwd"]) || $_POST["passwd"] == "") {
        return FALSE;
    }
    if (!isset($_POST["re-passwd"]) || $_POST["re-passwd"] == "") {
        return FALSE;
    }
    if ($_POST["passwd"] != $_POST["re-passwd"]) {
        return FALSE;
    }
    if (!isset($_POST["email"]) || $_POST["email"] == "") {
        return FALSE;
    }
    return TRUE;
}
session_start();
$_SESSION['user'] = 'guest';
session_regenerate_id();

$validationRules = [
    'u_name' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-žA-Ž0-9]+$/"
        ]
    ],
    'passwd' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-žA-Ž0-9]{4,}$/"
        ]
    ],
    'passwd' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-žA-Ž0-9]{4,}$/"
        ]
    ],
    'email' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-zA-Z0-9.]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/"
        ]
    ]
    
];

//$data = filter_input_array(INPUT_POST, $validationRules);
$_POST = filter_input_array(INPUT_POST, $validationRules);

require_once 'database_knjigarna.php';
$url = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_SPECIAL_CHARS);
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="styl.css">
        <meta charset="UTF-8" />
        <title>Login</title>
    </head>
    <body>
        <?php
        //var_dump($_POST);
            //registracija novegauporabnika
            if (isset($_POST["bttn"]) && $_POST["bttn"] == "Register") {
                ?>
                <div id="login">
                    <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                        <?php
                        if(isset($_POST["u_name"]) && $_POST["u_name"] != "") {
                            $username = $_POST["u_name"];
                            ?><input type="text" name="u_name" value="<?= $username ?>"><br/><?php
                        }
                        else {
                            ?><input type="text" name="u_name" placeholder="User name"><br/><?php
                        }                                   
                        ?>
                            <input type="password" name="passwd" placeholder="password"><br/>
                            <input type="password" name="re-passwd" placeholder="re-type password"><br/>
                            <input type="text" name="email" placeholder="e-mail"><br/>
                            <input type="submit" name="bttn" value="Confirm" /><br/>
                    </form>
                </div>  
                <?php
            }
            elseif (isset($_POST["bttn"]) && $_POST["bttn"] == "Confirm") {
                //vnos novega uporabnika v bazo podatkov
                if(legit()){
                    //MySQL vnos
                    $username = $_POST["u_name"];
                    $password = $_POST["passwd"];
                    $mail = $_POST["email"];
                    DBUsers::registerUser($username, $password, $mail)
                    ?>
                    <div id="login">
                        <div class="succ">
                            registration was successful<br/>
                        </div>
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                            <input type="hidden" name="u_name" value="<?= $username ?>">
                            <input type="submit" name="bttn" value="Login" /><br/>
                        </form>
                    </div>
                    <?php
                }
                //napaka pri vnosu podatkov za regstracijo
                else {
                    ?>
                    <div id="login">
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                            <?php
                            
                            if(isset($_POST["u_name"]) && $_POST["u_name"] != "") {
                                $username = $_POST["u_name"];
                                ?><input type="text" name="u_name" value="<?= $username ?>"><br/><?php
                            }
                            else {
                                ?>
                                <div class="error">
                                    Missing or invalid username <br/>
                                </div>
                                <input type="text" name="u_name" placeholder="user name"><br/><?php
                            }
                            if(!isset($_POST["passwd"]) || $_POST["passwd"] == "" || !isset($_POST["re-passwd"]) || $_POST["passwd"] != $_POST["re-passwd"]) {
                                ?>
                                    <div class="error">
                                        Missing or invalid password <br/> 
                                    </div>  
                                <?php
                            }
                            ?>
                                <input type="password" name="passwd" placeholder="password"><br/>
                                <input type="password" name="re-passwd" placeholder="re-type password"><br/>
                            <?php
                            
                            if(isset($_POST["email"]) && $_POST["email"] != "") {
                                $mail = $_POST["email"];
                                ?><input type="text" name="email" value="<?= $mail ?>"><br/><?php
                            }
                            else {
                                ?>
                                <div class="error">
                                    Missing or invalid email <br/>
                                </div>
                                <input type="text" name="email" placeholder="e-mail"><br/><?php
                            }                                   
                            ?>
                                <input type="submit" name="bttn" value="Confirm" /><br/>
                        </form>
                    </div>  
                    <?php
                }
            }
            else if(isset($_POST["u_name"]) && !isset($_POST["passwd"]) && $_POST["u_name"] != "") {
                $is_user = DBUsers::findUser($_POST["u_name"]);
                $username = $_POST["u_name"];
                if(count($is_user) > 0) {
                    $username = $_POST["u_name"];
                    ?>
                    <div id="login">
                        <form action="<?= $_SERVER["PHP_SELF"]?>" method="post">
                            <p><?= $username ?></p><br/>
                            <input type="hidden" name="u_name" value="<?= $username ?>">
                            <input type="password" name="passwd" placeholder="password"><br/>
                            <div class="buton">
                            <input type="submit" name="bttn" value="login" /><br/>
                            </div>
                        </form>
                    </div>
                    <?php
                }
                else {
                    ?>
                    <div id="login">
                        <div class="register">
                            Username not existing, create new account?<br/>
                        </div>
                    <form action="<?= $_SERVER["PHP_SELF"]?>" method ="post">
                        <input type="text" name="u_name" value="<?= $username ?>" /><br/>
                        <div class="buton">
                        <input type="submit" name="bttn" value="Login" />
                        <input type="submit" name="bttn" value="Register" /><br/>
                        </div>
                    </form>
                    </div>
                <?php
                }
            }
            else if(isset($_POST["u_name"]) && isset($_POST["passwd"]) && $_POST["u_name"] != "" && $_POST["passwd"] != "") {
                $pass = DBUsers::fetchPass($_POST["u_name"], $_POST["passwd"]);
                //var_dump($pass);
                if(count($pass) > 0) {
                    $username = $_POST["u_name"];
                    $_SESSION['user'] = $username;
                    $_SESSION['user_id'] = (int)$pass[0]['id_shopper'];
                    $_SESSION['user_status'] = 'user';
                    $cart = DBBooks::loadCart($_SESSION['user_id']);
                    if (count($cart) > 0)  {
                        $tmp_cart = unserialize($cart[0]['hasher']);
                        $_SESSION['cart'] = $tmp_cart;
                    }
                    if($pass[0]['status'] == 'user') {
                    ?>
                    <div id="login">
                        <div class ="succ">
                            Login successful<br/>
                        </div>
                        <form action="./index.php" method="get">
                            <div class="buton">
                            <input type="submit" value="OK"/><br/>
                            </div>
                        </form>
                    </div>
                    <?php
                    }
                    else {
                        $redirect = "https://" . $_SERVER['HTTP_HOST'] . "/netbeans/eshop/auth/index.php";
                        header("Location: ".$redirect);
                    }
                }
                else {
                    $username = $_POST["u_name"];
                    ?>
                    <div id="login">
                        <form action="<?= $_SERVER["PHP_SELF"]?>" method="post">
                            <p><?= $username ?></p><br/>
                            <input type="hidden" name="u_name" value="<?= $username ?>">
                            <input type="password" name="passwd" placeholder="password"><br/>
                            <div class="error">
                                <p>Wrong password</p>
                            </div>
                            <div class="buton">
                            <input type="submit" name="bttn" value="login" /><br/>
                            </div>
                        </form>
                    </div>
                    <?php
                }
            }
            else {
                ?>
                <div id="login">
                <form action="<?= $_SERVER["PHP_SELF"]?>" method ="post">
                    <input type="text" name="u_name" placeholder="user name" /><br/>
                    <div class="buton">
                    <input type="submit" name="bttn" value="Login" /><br/>
                    </div>
                </form>
                </div>
            <?php
            }
        ?>
    </body>
</html>