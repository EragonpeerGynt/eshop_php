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

function checkerMine($id_seller, $id_order) {
    $tmp = DBOrders::doingMyPart($id_seller, $id_order);
    foreach ($tmp as $key => $raw) {
        if($raw['status'] == 0) {
            return "";
        }
        elseif($raw['status'] == 1) {
            return " order--complete";
        }
        elseif($raw['status'] == 2) {
            return " order--deny";
        }
    }
    
    return "";
}

function orderSubmited($id_o, $id_s) {
    $tmp = DBOrders::currStatus($id_o)[0];
    if($tmp['canceled'] == 1) {
        return " order--cancel";
    }
    else {
        return checkerMine($id_s, $id_o);
    }
    
}

function getItDone($data) {
    ?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="styl.css">
        <meta charset="UTF-8" />
        <title>e-knjigarna</title>
    </head>
    <body>
        <div id="edit edit--special">
                    <form action="./index.php" method="post">
                        <button type="submit" name="go" value="go">Home</button><br/>
                    </form>
        </div>
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
                    echo '<div class="order'. orderSubmited($raw['id_order'], $_SESSION['user_id']).'">';
                    echo "Order tracking number: ".$raw['id_order']."<br/>";
                    if (orderSubmited($raw['id_order'], $_SESSION['user_id']) == "") {    
                        ?>
                        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                            <input type="hidden" name="pending" value="<?= $_SESSION['user_id'] ?>">
                            <input type="hidden" name="id_order" value="<?= $raw['id_order'] ?>">
                            <input type="submit" name="admin_control" value="Order complete"><input type="submit" name="admin_control" value="Cancel order">
                        </form>
                        <?php
                    }
                }
                ?>
                <div class="book<?= status($raw['status']) ?>">
                    <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                        <input type="hidden" name="id" value="<?= $raw["id_book"] ?>" />
                        <p><?= $raw['quantity'] ?>x <?= $raw["author_name"] ?> : <?= $raw["title"] ?></p>
                        <p><?= number_format($raw["price"]*$raw['quantity'], 2) ?> EUR<br/>
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

function orderinos($data) {
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="styl.css">
        <meta charset="UTF-8" />
        <title>e-knjigarna</title>
    </head>
    <body>
        <div id="edit edit--special">
                    <form action="./index.php" method="post">
                        <button type="submit" name="go" value="go">Home</button><br/>
                    </form>
        </div>
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
                        <p><?= $raw['quantity'] ?> <?= $raw["author_name"] ?> : <?= $raw["title"] ?></p>
                        <p><?= number_format($raw["price"]*$raw['quantity'], 2) ?> EUR<br/>
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

$validationRules = [
    'history' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^1$/"
        ]
    ],
    'id_order' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[0-9]+$/"
        ]
    ],
    'admin_control' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^(Order complete|Cancel order)$/"
        ]
    ],
    'id' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[0-9]+$/"
        ]
    ],
    'pending' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[0-9]+$/"
        ]
    ],
    'terminate' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options' => [
            'regexp' => "/^[0-9]+$/"
        ]
    ]
];

//$data = filter_input_array(INPUT_POST, $validationRules);
$_POST = filter_input_array(INPUT_POST, $validationRules);
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

if (isset($_POST['history']) && ($_SESSION['user_status'] != 'seller' || $_SESSION['user_status'] != 'admin')) {
    $ord = DBOrders::getHistory($_SESSION['user_id']);
    //var_dump($ord);
    orderinos($ord);
}

if (isset($_POST['admin_control']) && $_POST['admin_control'] == 'Order complete') {
    DBOrders::orderFinished($_POST['id_order'], $_SESSION['user_id']);
    $ord = DBOrders::getPending($_SESSION['user_id']);
    getItDone($ord);
}

if (isset($_POST['admin_control']) && $_POST['admin_control'] == 'Cancel order') {
    DBOrders::orderDeny($_POST['id_order'], $_SESSION['user_id']);
    $ord = DBOrders::getPending($_SESSION['user_id']);
    getItDone($ord);
}

if (isset($_POST['pending']) && ($_SESSION['user_status'] == 'seller' || $_SESSION['user_status'] == 'admin')) {
    $ord = DBOrders::getPending($_SESSION['user_id']);
    getItDone($ord);
}

