<?php
require "./stupid/init.php";
require "./stupid/tag.php";
require "./blocks/head.phtml";

if (isset($_POST["confirm"])) createTag($db, $_POST["tagName"], $_POST["tagDesc"], "THING");

//I don't need this now, tags are created when things are created and the tag doesn't exist yet
//if (isset($_SESSION["user"]))//only users can create tags >:/ 
//if (isset($_POST["createTag"])) require "./tagCreate.phtml";
$list = listTags($db, "THING");

require "./thingTags.phtml";
require "./blocks/tail.phtml";
?>