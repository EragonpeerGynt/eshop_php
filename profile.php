<?php
session_start();
//var_dump($_POST);
$validationRules = [
    'username' => [
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
    'email' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-zA-Z0-9\.\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z0-9\-]+$/"
        ]
    ],
    'passwd1' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-žA-Ž0-9]{4,}$/"
        ]
    ],
    'passwd2' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-žA-Ž0-9]{4,}$/"
        ]
    ],
    'commit' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^(username|passwd|email)$/"
        ]
    ],
    'iden' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[0-9]+$/"
        ]
    ],
    'edit' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-žA-Ž0-9]+$/"
        ]
    ],
    'change' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^(username|passwd|email)$/"
        ]
    ],
    'user' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-žA-Ž0-9]+$/"
        ]
    ],
    'passwd' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-žA-Ž0-9]+$/"
        ]
    ],
    'mail' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[a-zA-Z0-9\.\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z0-9\-]+$/"
        ]
    ]
];

//$data = filter_input_array(INPUT_POST, $validationRules);
$_POST = filter_input_array(INPUT_POST, $validationRules);
//var_dump($_POST);
$url = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_SPECIAL_CHARS);
if(!isset($_SESSION['user']) || ($_SESSION['user'] == "guest" && $_SESSION['user_id'] == 'guest')) {
    session_destroy();
    ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>ERROR</title>
        </head>
        
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
else if (isset($_POST['change']) && $_POST['change'] != "") { ?>
    <html>
        <head>
            <link rel="stylesheet" type="text/css" href="styl.css">
            <meta charset="UTF-8" />
            <title>Profile</title>
        </head>
        <body>
            <div id="login">
            <?php
            if($_POST['change'] == 'username') {
                ?>
                <form action="<?= $_SERVER["PHP_SELF"]?>" method="post">
                    If you want to change your user name to: <br/>
                    <input type="text" name="username" value="<?= $_POST['user'] ?>"><br/>
                    please enter password:<br/>
                    <input type="password" name="passwd" placeholder="Password"><br/>
                    <button type="submit" name="commit" value="username">Confirm</button>
                </form>
                <?php
            }
            else if($_POST['change'] == 'passwd') {
                ?>
                <form action="<?= $_SERVER["PHP_SELF"]?>" method="post">
                    <input type="password" name="passwd1" value="<?= $_POST['passwd'] ?>"><br/>
                    Please re-enter password:<br/>
                    <input type="password" name="passwd2" placeholder="Password"><br/>
                    Enter old password:<br/>
                    <input type="password" name="passwd" placeholder="Password"><br/>
                    <button type="submit" name="commit" value="passwd">Confirm</button>
                </form>
                <?php
            }
            else if($_POST['change'] == 'email') {
                ?>
                <form action="<?= $_SERVER["PHP_SELF"]?>" method="post">
                    If you want to change your e-mail to: <br/>
                    <input type="text" name="email" value="<?= $_POST['mail'] ?>"><br/>
                    please enter password:<br/>
                    <input type="password" name="passwd" placeholder="Password"><br/>
                    <button type="submit" name="commit" value="email">Confirm</button>
                </form>
                <?php
            }
            ?>
            </div>
        </body>
    </html>
<?php
}
else if (isset($_POST['commit']) && $_POST['commit'] != "") {
    //var_dump($_POST);
    require_once 'database_knjigarna.php';
    $fine = DBUsers::secureConnect($_SESSION['user_id'], $_SESSION['user'], $_POST['passwd']);
    //var_dump($fine);
    //var_dump($_SESSION);
    //var_dump($fine);
    if(count($fine) == 1) {
        if (isset($_POST['username'])) {
            DBUsers::updateAtribute($_SESSION['user_id'], $_POST['username'], $_POST['commit'], $_POST['passwd']);
            $_SESSION['user'] = $_POST['username'];
        }
        else if (isset($_POST['passwd1']) && isset($_POST['passwd2']) && $_POST['passwd1'] == $_POST['passwd2']) {
            DBUsers::updateAtribute($_SESSION['user_id'], $_POST['passwd2'], $_POST['commit'], $_POST['passwd2']);
        }
        else if (isset($_POST['email'])) {
            DBUsers::updateAtribute($_SESSION['user_id'], $_POST['email'], $_POST['commit'], $_POST['passwd']);
        }
        ?>
        <html>
            <head>
                <link rel="stylesheet" type="text/css" href="styl.css">
                <meta charset="UTF-8" />
                <title>Profile</title>
            </head>
            <body>
                <div id="login">
                    <div class="succ">
                        Change was successful<br/>
                    </div>
                    <form action="./index.php" method="get">
                        <button type="submit">To store</button>
                    </form>
                    <form action="./profile.php" method="post">
                        <input type="hidden" name="iden" value="<?= $_SESSION['user_id'] ?>">
                        <input type="hidden" name="edit" value="<?= $_SESSION['user'] ?>">
                        <button type="submit">To profile</button>
                    </form>
                </div>
            </body>
        </html>
        <?php
    }
    else {
        ?>
        <html>
            <head>
                <link rel="stylesheet" type="text/css" href="styl.css">
                <meta charset="UTF-8" />
                <title>ERROR</title>
            </head>

                <div id="login">
                    <div class="error">
                        There seems to be an error<br/>
                    </div>
                    <form action="./profile.php" method="get">
                        <button type="submit">Continue</button>
                    </form>
                </div>

            </body>
        </html>
        <?php
    }
}
else {
    require_once 'database_knjigarna.php';
    $url = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_SPECIAL_CHARS);
    if(isset($_POST['edit']) && $_POST['edit'] == 'Logout') {
        session_destroy();
        ?>
        <html>
            <head>
                <link rel="stylesheet" type="text/css" href="styl.css">
                <meta charset="UTF-8" />
                <title>Logout</title>
            </head>
            <body>
                <div id="login">
                    <form action="./index.php" method="get">
                        <div class="succ">
                        Log out was successful<br/>
                        </div>
                        <input type="submit" value="OK"/><br/>
                    </form> 
                </div>
            </body>
        </html>
        <?php
    }
    else {
        ?>
        <html>
            <head>
                <link rel="stylesheet" type="text/css" href="styl.css">
                <meta charset="UTF-8" />
                <title>Profile</title>
            </head>
            <body>
                <div id="login">
                    <?php
                        try {
                            $user_id = $_SESSION['user_id'];
                            $tmp = DBUsers::getData($user_id);
                            $mail = $tmp[0]['email'];
                            $user = $tmp[0]['u_name'];
                        }
                        catch (Exception $e) {
                            echo "<div class='error'>There seems to be an error: {$e->getMessage()} </div>";
                        }
                    ?>
                    Trenutno urejate podatke uporabnika:<br/>
                    <form action="<?= $_SERVER["PHP_SELF"]?>" method="post">
                        <input type="text" name="user" value="<?= $user ?>"><br/>
                        <button type="submit" name="change" value="username">Change</button>
                    </form>
                    <form action="<?= $_SERVER["PHP_SELF"]?>" method="post">
                        Password:<br/>
                        <input type="password" name="passwd" placeholder="Password"><br/>
                        <button type="submit" name="change" value="passwd">Change</button>
                    </form>
                    <form action="<?= $_SERVER["PHP_SELF"]?>" method="post">
                        E-mail<br/>
                        <input type="text" name="mail" value="<?= $mail ?>"><br/>
                        <button type="submit" name="change" value="email">Change</button>
                    </form>
                </div>
                
                <div id="edit">
                    <form action="./edit_contact.php" method="post">
                        <button type="submit" name="editorial" value="<?= $_SESSION['user_id'] ?>">Edit contact<br/>information</button><br/>
                    </form>
                    <?php
                    if ($_SESSION['user_status'] == 'user') {
                    ?>
                    <form action="./my-orders.php" method="post">
                        <button type="submit" name="history" value="1">View order<br/>history</button><br/>
                    </form>
                    <?php
                    }
                    if ($_SESSION['user_status'] == "admin" || $_SESSION['user_status'] == "seller") {
                    ?>
                    <form action="./edit_contact.php" method="post">
                        <button type="submit" name="editorial" value="find_user">Edit user<br/>information</button><br/>
                    </form>
                    <?php
                    }
                    if ($_SESSION['user_status'] == "admin" || $_SESSION['user_status'] == "seller") {
                    ?>
                    <form action="./edit_book.php" method="post">
                        <button type="submit" name="editorial" value="<?= $_SESSION['user_id'] ?>">Edit my<br/>published<br/>books<br/>information<br/>or add new<br/>book</button><br/>
                    </form>
                    <?php
                    }
                    if ($_SESSION['user_status'] == 'seller' || $_SESSION['user_status'] == 'admin') {
                    ?>
                    <form action="./my-orders.php" method="post">
                        <button type="submit" name="pending" value="1">View<br/>pending<br/>orders</button><br/>
                    </form>
                    <?php
                    }
                    ?>
                </div>
                
                
            </body>             
        </html>
        <?php
    }
}
?>