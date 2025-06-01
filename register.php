<?php
require "./stupid/init.php";
require "./stupid/user.php";
require "./blocks/head.phtml";
require "./register.phtml";

if (isset($_POST["register"])){
    if ($_POST["pass"]===$_POST["passCheck"]){
        registerUser($db, $_POST["name"], $_POST["pass"], $_FILES["image"]);
    }
    else echo "<h2> heslo zopakovat pls <h2>";
}

require "./blocks/tail.phtml";


?>