<?php
require "./stupid/init.php";
require "./stupid/tag.php";
require "./blocks/head.phtml";

if (isset($_POST["joinGroup"])){
    joinTag($db,$_POST["tagId"], $_SESSION["user"]["userId"]);
}
if (isset($_POST["leaveGroup"])){
    separateTag($db, $_POST["tagId"], $_SESSION["user"]["userId"]);
}
// the groups table is useless! Just make groups using tagTypes!
//good point there goes another 3 hours of work :)

if (isset($_SESSION["user"]))//only users can create groups >:/ 
if (isset($_POST["createGroup"])) require "./groupCreate.phtml";
$list = listTags($db, "GROUP");
//var_dump($list);
require "./groups.phtml";
require "./blocks/tail.phtml";
if (isset($_POST["create"])) {
    createTag($db, $_POST["groupName"], $_POST["groupDesc"], "GROUP");    
}
//join and leave functions

?>