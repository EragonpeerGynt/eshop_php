<?php 
session_start();
require_once '../database_knjigarna.php';
$url = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_SPECIAL_CHARS);
if(!isset($_SESSION['user']) || $_SESSION['user_id'] == 'guest') {
    $redirect = "https://" . $_SERVER['HTTP_HOST'] . "/netbeans/eshop/index.php";
    header("Location: ".$redirect);
}

$client_cert = filter_input(INPUT_SERVER, "SSL_CLIENT_CERT");

if ($client_cert == null) {
    $redirect = "https://" . $_SERVER['HTTP_HOST'] . "/netbeans/eshop/index.php";
    header("Location: ".$redirect);
}

$data = DBUsers::getData($_SESSION['user_id']);
if ($data[0]['status'] == 'user') {
    $redirect = "https://" . $_SERVER['HTTP_HOST'] . "/netbeans/eshop/index.php";
    header("Location: ".$redirect);
}

?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../styl.css">
        <meta charset="UTF-8" />
        <title>Authorization</title>
    </head>
    <body>
<?php

$cert_data = openssl_x509_parse($client_cert);
$commonname = (is_array($cert_data['subject']['CN']) ?
        $cert_data['subject']['CN'][0] : $cert_data['subject']['CN']);
if ($cert_data['hash'] == $data[0]['hash']) {
    $_SESSION['status'] = $data[0]['status'];
    //echo "success";
    ?>
    <div id="login">
        <div class ="succ">
            Authorization successful<br/>
        </div>
        <form action="../index.php" method="get">
            <div class="buton">
            <input type="submit" value="OK"/><br/>
            </div>
        </form>
    </div>
    <?php
}
else {
    //echo "failiure";
    ?>
    <div id="login">
        <div class ="succ">
            Authorization failed<br/>
        </div>
        <form action="../index.php" method="get">
            <div class="buton">
            <input type="submit" value="OK"/><br/>
            </div>
        </form>
    </div>
    <?php
}
?>

    </body>