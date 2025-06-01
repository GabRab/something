<?php
require "./stupid/init.php";
require "./stupid/user.php";
if (isset($_POST["confirm"])){
    loginUser($db, $_POST["name"], $_POST["pass"]);
}

require "./blocks/head.phtml";

require "./login.phtml";

require "./blocks/tail.phtml";
?>