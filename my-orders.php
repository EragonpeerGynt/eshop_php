<?php

function status($data) {
    $chooser[0] = '';
    $chooser[1] = ' book--green';
    $chooser[2] = ' book--red';
    echo $chooser[$data];
}

function complete($id_o) {
    $ord = DBOrders::getSingleHistory($_SESSION['user_id'], $id_o);
    foreach ($ord as $key => $raw) {
        if($raw['status'] == 0 || $raw['status'] == 2) {
            return false;
        }
    }
    return true;
}

function orderCancel($order_id, $order_canceled) {
    if (complete($order_id)) {
        return " order--complete";
    }
    elseif ($order_canceled == 1) {
        return " order--cancel";
    }
    else {
        return "";
    }
}

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
            <div>
            <?php
            $prev = -1;
            foreach($data as $key => $raw) {
                if ($prev != $raw['id_order']) {
                    $prev = $raw['id_order'];
                    ?>
                    </div>
                    <?php
                    echo "<br/><br/>";
                    echo '<div class="order'.orderCancel($prev, $raw['canceled']).'">';
                    echo "order tracking number: ".$raw['id_order']."<br/>";
                    if (orderCancel($prev, $raw['canceled']) == "") {
                        ?>
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                            <input type="hidden" name="history" value="1">
                            <input type="hidden" name="terminate" value="<?= $raw['id_order'] ?>">
                            <input type="submit" value="Cancel order">
                        </form>
                        <?php
                    }
                }
                ?>
                <div class="book<?= status($raw['status']) ?>">
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

//$_POST['history'] = 1;

if (isset($_POST['terminate'])) {
    DBOrders::cancelOrder($_POST['terminate']);
}

if (isset($_POST['history'])) {
    $ord = DBOrders::getHistory($_SESSION['user_id']);
    //var_dump($ord);
    orderinos($ord);
}

