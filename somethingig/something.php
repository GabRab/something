<?php


/*I need:
-main stream (sorted by age: new on top, old on bottom)
    -many sorting options, with additive tags, which show what they mean
    -everything has a universal tag for "all"
    -advanced tags include images, videos, text-only which get applied based on the contents of the message 
    -time tags based on age of messages
    -not being able to send messages, or like them if not signed in
    -tags like user:something when searching
    -18+ tags for logged in users behind bars
-groups/fanbases (just make it a tag bruh)
-themes/customization


-idea : change message instead of deleting it completely
!make sure to change params after finishing tags!

*/
require "./stupid/init.php";
require "./stupid/thing.php";
require "./blocks/head.phtml";

if (isset($_POST["finish"])){//if form filled, add to database and show
    createThing($db, $_SESSION["user"]["userId"],$_POST["thingText"], $_FILES["thingFile"], "ALL");
}
if (isset($_POST["create"])){
    require "./somethingCreate.phtml";//opens form for inputs
}

if (isset($_POST["search"])){   
    $tags= $_POST["search"];
    $things = listSomething($db, $tags);
}
else {
    $tags= "ALL";
    $things = listSomething($db, $tags);
}
if (isset($_POST["remove"])){
    removeThing($db, $_POST["thingID"]);
}
if (isset($_POST["edit"])){
    (int) $GLOBALS["editId"] = $_POST["editID"];
    $GLOBALS["editText"] = $_POST["editText"];
    $GLOBALS["editFile"] = $_POST["editFile"];
    $GLOBALS["editType"] = $_POST["editType"];
    require "./somethingEdit.phtml";
}
if (isset($_POST["somethingEdit"])){
    $edit =editThing($db, $_POST["IdChange"], $_POST["thingTextNew"], $_FILES["thingFileNew"], "ALL");
    var_dump($edit);
}
echo "<br>";

require "./something.phtml";
require "./blocks/tail.phtml";
?>