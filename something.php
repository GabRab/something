<?php

require "./stupid/init.php";
require "./stupid/thing.php";
require "./blocks/head.phtml";

//IMMA set this as one if it doesn't exist, then Imma put page select at bottom, so I don't have to make a new request at server for amount of things
//require "./somethingPages.phtml";//gonna need to fetch [amount of things on page]+1 (to check if there is another page available) using LIMIT

//something list
//sets tag as false to show everything, unless changed by search
//changes page number
if (!isset($_SESSION["pageNumber"])) $_SESSION["pageNumber"] = 0;//initial page number, so it doesn't jump elsewhere when you check out a thing
$_SESSION["some"]["tags"] = false;
if (isset($_POST["unprevious"]))$_SESSION["pageNumber"]+=1;
if (isset($_POST["previous"]))$_SESSION["pageNumber"]-=1;
if ($_SESSION["pageNumber"]<0) $_SESSION["pageNumber"]=0;
//changes tags, to search for specific things
if (isset($_POST["search"])) {$_SESSION["some"]["tags"]=$_POST["searchStr"]; $_SESSION["pageNumber"]=0;}//so that pageId doesn't mess up (I'm going)
//since I want to keep searched text, Imma move this here
require "./search.phtml";
$things = listSomething($db, $_SESSION["some"]["tags"], $_SESSION["pageNumber"]);    
require "./somethingPages.phtml";
if (isset($_POST["create"])){
    require "./somethingCreate.phtml";//opens form for inputs
}
//finally gets things from server and shows them using something.phtml
//$things = listSomething($db, $_POST["searchStr"], $_SESSION["pageNumber"]);    
require "./something.phtml";

//for js, so it can hide page buttons
$_SESSION["some"]["thingAmount"]=count($things);
if (isset($things[10])) array_splice($things, 10, 1);



if (isset($_POST["finish"])){//if form filled, add to database and show
    createThing($db, $_SESSION["user"]["userId"],$_POST["thingText"], $_FILES["thingFile"], $_POST["thingTags"]);
}
if (isset($_POST["showComms"])||isset($_POST["finishComm"])){//if wants comms show them
    $_SESSION["comment"]["thingId"]=$_POST["showOfThing"];
    header("Location: /somethingig/somethingComms.php");
}





if (isset($_POST["remove"])){
    removeThing($db, $_POST["editId"]);
}
if (isset($_POST["edit"])){
    (int) $GLOBALS["editId"] = $_POST["editId"];
    $GLOBALS["editText"] = $_POST["editText"];
    $GLOBALS["editFile"] = $_POST["editFile"];
    $GLOBALS["editType"] = $_POST["editType"];
    require "./somethingEdit.phtml";
    var_dump($_SESSION["some"]["tags"]);
}
if (isset($_POST["somethingEdit"])){
    //$edit =
    editThing($db, $_POST["IdChange"], $_POST["thingTextNew"], $_FILES["thingFileNew"], $_POST["thingTagNew"]);
    //var_dump($edit);
}
echo "<br>";
require "./somethingPages.phtml";
require "./blocks/tail.phtml";
/*27.05.2025
    searching with tags, and includes in text..........done
    creating things with tags..........................done 
    comments...........................................done (no tags for this one?, nah probably not. Though I would like to search for things of a specific guy, maybe later)
    showing tagsConnected to things....................done :D 

*/
?>
<script>

</script>