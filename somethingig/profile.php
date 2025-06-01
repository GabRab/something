<?php
require "./stupid/init.php";
require "./stupid/user.php";
require "./blocks/head.phtml";


    if (isset($_POST["change"])){
        require "./profileChange.phtml";
        require "./profileImage.phtml";
    }
    if (isset($_POST["changeName"])) changeName($db, $_POST["name"]);
    if (isset($_POST["changePass"])&&($_POST["newPass"]===$_POST["newPassCheck"])) changePass($db, $_POST["pass"], $_POST["newPass"]);
    if (isset($_POST["changeDesc"])) changeDesc($db, $_POST["desc"]);
    if (isset($_POST["changeImage"])) changeImage($db, $_FILES["image"]);
    else require "./profile.phtml";
    if (isset($_POST["logout"])){
        // $_SESSION["user"] = NULL;  furt bude tam, ale prazdne
        unset($_SESSION["user"]); //  uz tam nebude
        header("location: /SOMETHINGIG/something.php");
    }

    

require "./blocks/tail.phtml";

?>