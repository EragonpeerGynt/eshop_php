<?php
session_start();
if((!isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'off')) && (isset($_SESSION["user_id"]) && $_SESSION["user_id"] != "guest")) {
    $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".$redirect);
    //exit;
    session_regenerate_id();
}
if(!isset($_SESSION['user'])) {
    $_SESSION['user'] = "guest";
    $_SESSION['user_id'] = "guest";
}

require_once 'database_knjigarna.php';
$url = filter_input(INPUT_SERVER, "PHP_SELF", FILTER_SANITIZE_SPECIAL_CHARS);

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

?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="styl.css">
        <meta charset="UTF-8" />
        <title>e-knjigarna</title>
    </head>
    <body>
        
        <!--izpišemo vse knjige-->
        <div id="main">
            <div class="empty">
                
            </div>
            <?php
            try {
                $all_books = DBBooks::getAllBooks();
            } catch (Exception $e) {
                echo "Prišlo je do napake: {$e->getMessage()}";
            } ?>
            <?php foreach ($all_books as $tmp => $knjiga):
                if ($knjiga['hidden'] == 1) {
                    continue;
                }
                ?>
                <div class="book">
                    <form action="<?= $url ?>" method="post">
                        <input type="hidden" name="do" value="add_into_cart" />
                        <input type="hidden" name="id" value="<?= $knjiga["id_book"] ?>" />
                        <p><?= $knjiga["author_name"] ?> : <?= $knjiga["title"] ?></p>
                        <p><?= number_format($knjiga["price"], 2) ?> EUR<br/>
                            <button type="submit">V košarico</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php
        // put your code here
        echo "Delam"
        ?>
        <br/>
        <div id="heder">
            <div class="hed1">E-Knjigarna</div>
            
            <div class="user">
                <?php
                if($_SESSION['user_id'] != "guest") {
                    ?>
                    <form action="./profile.php" method="post">
                        Welcome 
                        <input type="hidden" name="iden" value="<?= $_SESSION['user_id'] ?>">
                        <input type="submit" name="edit" value="<?= $_SESSION['user'] ?>">
                        <input type="submit" name="edit" value="Logout"><br/>
                    </form>
                    <?php
                }
                else {
                    ?>
                    <form action="./login.php" method="post">
                        Welcome <?= $_SESSION['user'] ?>
                        <input type="submit" name="edit" value="Login/Register"><br/>
                    </form>
                    <?php
                }
                ?>
            </div>
        
        <div id="sidebar">
            <div class="boxbox">
                
            
            
            
            <div class="vozi">
                <?php
                $s_cart = isset($_SESSION["cart"]) ? $_SESSION["cart"] : [];
                if ($s_cart):
                $total = 0;

                foreach ($s_cart as $id => $amount):
                    $book = DBBooks::getBook($id)[0];
                    $total += $book['price'] * $amount;
                    ?>
                    <form action="<?= $url ?>" method="post">
                        <input type="hidden" name="do" value="update_cart" />
                        <input type="hidden" name="id" value="<?= $book['id_book']?>" />
                        <input type="number" name="kolicina" value="<?= $amount ?>"
                               class="short_input" />
                        &times; <?=
                        (strlen($book['title']) < 30) ?
                                $book['title'] :
                                substr($book['title'], 0, 26) . " ..."
                        ?> 
                        <button type="submit">Update</button> 
                    </form>
                <?php endforeach; ?>

                <p>Total: <b><?= number_format($total, 2) ?> EUR</b></p>

                <form action="<?= $url ?>" method="POST">
                    <input type="hidden" name="do" value="purge_cart" />
                    <input type="submit" value="Izprazni košarico" />
                </form>
            <?php else: ?>
                <h3>Shopping cart is empty</h3>                
            <?php endif; ?>
                
                
            </div>  
            </div>
        </div>
        </div>
    </body>
</html>
