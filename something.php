<?php

require "./stupid/init.php";
require "./stupid/thing.php";
require "./blocks/head.phtml";

//IMMA set this as one if it doesn't exist, then Imma put page select at bottom, so I don't have to make a new request at server for amount of things
//require "./somethingPages.phtml";//gonna need to fetch [amount of things on page]+1 (to check if there is another page available) using LIMIT

/*
if (!isset($pageNum)) $pageNum = 0;//initial page number, so it doesn't jump elsewhere when you check out a thing
if (isset($_GET["unprevious"]))$pageNum+=1;
if (isset($_GET["previous"]))$pageNum-=1;
if ($pageNum<0) $pageNum=0;
*/

if (!isset($_GET["pageNum"])) $_GET["pageNum"]=0;
var_dump($_GET["pageNum"]);
//show everything if no tags
$_SESSION["some"]["tags"] = false;
//changes tags, to search for specific things
if (isset($_POST["search"])) {$_SESSION["some"]["tags"]=$_POST["searchStr"]; $pageNum=0;}//so that pageId doesn't mess up (I'm going)
//since I want to keep searched text, Imma move this here
require "./search.phtml";
$maxPage = intval(getMaxPage($db));//max page count
var_dump($maxPage);
if(intval($_GET["pageNum"])>intval($maxPage)) $things = listSomething($db, $_SESSION["some"]["tags"], $maxPage);    

if (isset($_POST["create"])){
    require "./somethingCreate.phtml";//opens form for inputs
}



if (isset($_POST["finish"])){//if form filled, add to database and show
    createThing($db, $_SESSION["user"]["userId"],$_POST["thingText"], $_FILES["thingFile"], $_POST["thingTags"]);
}

//comments

//close them if not needed :)
if (isset($_POST["closeComm"])) {
    unset($_GET["showComms"]);
}
//show comments
if (isset($_GET["showComms"])){
    if (isset($_POST["addComm"])) createComm($db, $thing["thingId"], $_POST["commText"], $_FILES["commFile"]);
    $thing = getThingStuff($db, $_GET["showComms"], "things");
    $thing = $thing[0];
    $thingComms = getThingStuff($db, $_GET["showComms"], "comments");
    require "./somethingComms.phtml";
    echo "<br><br><br>".var_dump($thing)."<br><br><br>".var_dump($thingComms);
    
    //header("Location: /somethingig/somethingComms.php");
}

//show things

else {
    $things = listSomething($db, $_SESSION["some"]["tags"], $_GET["pageNum"]);    
    require "./somethingPages.phtml";
    require "./something.phtml";
    require "./somethingPages.phtml";
}




if (isset($_POST["remove"])){
    removeThing($db, $_POST["editId"]);
}
if (isset($_POST["edit"])){
    (int) $editId = $_POST["editId"];
    $editText = $_POST["editText"];
    $editFile = $_POST["editFile"];
    $editType = $_POST["editType"];
    require "./somethingEdit.phtml";
    var_dump($_SESSION["some"]["tags"]);
}
if (isset($_POST["somethingEdit"])){
    //$edit =
    editThing($db, $_POST["IdChange"], $_POST["thingTextNew"], $_FILES["thingFileNew"], $_POST["thingTagNew"]);
    //var_dump($edit);
}
echo "<br>";
require "./blocks/tail.phtml";
/*27.05.2025
    searching with tags, and includes in text..........done
    creating things with tags..........................done 
    comments...........................................done (no tags for this one?, nah probably not. Though I would like to search for things of a specific guy, maybe later)
    showing tagsConnected to things....................done :D 

*/
?>
<script>
    //page logic for multiple page selectors
    let switchBack = false;
    for (let i=0; i<document.getElementsByClassName("back").length;i++){
                if (document.getElementById("pageNumber").value<=0){
                document.getElementsByClassName("back")[i].style.visibility="hidden";
            }
                
                console.log(document.getElementsByClassName("back")[i]);
                document.getElementsByClassName("back")[i].addEventListener("click", ()=>{
                    
                    if (!switchBack){
                        switchBack = true;
                        document.getElementById("pageNumber").value--;
                    }
                    document.getElementById("pages").submit();
                })
    }
    let switchForth =false;//switch for updating pageNumber
    for (let i=0; i<document.getElementsByClassName("forth").length;i++){//for multiple page selections (top and bottom, maybe somewhere else, but idk)
        console.log(i);
        if (<?=$maxPage?><= document.getElementById("pageNumber").value){
            document.getElementsByClassName("forth")[i].style.visibility="hidden";
        }
                console.log(document.getElementsByClassName("forth")[i]);
                document.getElementsByClassName("forth")[i].addEventListener("click", ()=>{
                    if (!switchForth){//makes sure that only sends once (goes to next page)
                        let switchForth = true;
                        document.getElementById("pageNumber").value++;
                    }
                    document.getElementById("pages").submit();
                })
            }


</script>