<?php
//get the database object from server
session_start();
$db = mysqli_connect("localhost", "root", "", "something");
if ($db===false){
    echo "<h1>Pripojeni k databazi selhalo</h1>";
    exit;
}

mysqli_set_charset($db, "utf8mb4")
?>