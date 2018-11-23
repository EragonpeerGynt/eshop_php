<?php
session_start();
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
    var_dump($_POST);
    require_once 'database_knjigarna.php';
    $fine = DBUsers::secureConnect($_SESSION['user_id'], $_SESSION['user'], $_POST['passwd']);
    var_dump($fine);
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
             
        </html>
        <?php
    }
}
?>