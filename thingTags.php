<?php
require "./stupid/init.php";
require "./stupid/tag.php";
require "./blocks/head.phtml";
//don't need to create tags, too troublesome to check which ones exist, so tags are made when USERs register and THINGs get created and tags are assigned to them, GROUPs on the other hand... I haven't even finished them ;-;

$thingTags = listTags($db, "THING");
$userTags = listTags($db, "USER");
$groupTags = listTags($db, "GROUP");
require "./thingTags.phtml";
require "./blocks/tail.phtml";
?>