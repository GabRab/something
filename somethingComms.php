<?php
require "./stupid/init.php";
require "./stupid/thing.php";
require "./blocks/head.phtml";
//I want to cry

    $thing = thingComms($db, $_SESSION["comment"]["thingId"], "things");//gets stuff about thing
    $thing = $thing[0];//I get $thing[0]=> array of stuff, so I remove it.
    $comms = thingComms($db, $_SESSION["comment"]["thingId"], "comments");//gets things comms

if (isset($_POST["finishComm"])&&isset($_SESSION["user"])){//thingId should be fine here, since we are in comms \ (".") \
    createComm($db, $thing["thingId"], $_POST["commText"], $_FILES["commFile"]);
}
require "./somethingComms.phtml";
require "./blocks/tail.phtml";
?>