<?php

function orderinos($data) {
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="styl.css">
        <meta charset="UTF-8" />
        <title>e-knjigarna</title>
    </head>
    <body>
        <div id="main">
            <?php
            $prev = -1;
            foreach($data as $key => $raw) {
                if ($prev != $raw['id_order']) {
                    $prev = $raw['id_order'];
                    echo "<br/><br/>order tracking number: ".$raw['id_order']."<br/>";
                }
                ?>
                <div class="book">
                    <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                        <input type="hidden" name="id" value="<?= $raw["id_book"] ?>" />
                        <p><?= $raw["author_name"] ?> : <?= $raw["title"] ?></p>
                        <p><?= number_format($raw["price"], 2) ?> EUR<br/>
                    </form>
                </div> 
            <?php
            }
            ?>
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

session_start();
//var_dump($_POST);
$url = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_SPECIAL_CHARS);
require_once 'database_knjigarna.php';
if(!isset($_SESSION['user']) || ($_SESSION['user'] == "guest" && $_SESSION['user_id'] == 'guest')) {
    session_destroy();
    errorReport("");
    exit();
}

$_POST['history'] = 1;

if (isset($_POST['history'])) {
    $ord = DBOrders::getHistory($_SESSION['user_id']);
    //var_dump($ord);
    orderinos($ord);
}

