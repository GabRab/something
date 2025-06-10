<?php

require "./stupid/init.php";
require "./stupid/thing.php";
require "./stupid/tag.php";
require "./blocks/head.phtml";

//NEED TO PUT PHP LOGIC AFTER/UNDER FORMS SO IT UPDATES FULLY
//idk it just doesn't work ;-;


/*
if (!isset($pageNum)) $pageNum = 0;//initial page number, so it doesn't jump elsewhere when you check out a thing
if (isset($_GET["unprevious"]))$pageNum+=1;
if (isset($_GET["previous"]))$pageNum-=1;
if ($pageNum<0) $pageNum=0;
*/
$tagList=[];
if (!isset($_GET["pageNum"])) $_GET["pageNum"]=0;
if (!isset($_GET["searchStr"])) $_GET["searchStr"]=null;

//changes tags, to search for specific things
if (isset($_GET["search"])) {
    $_GET["pageNum"]=0;
}//so that pageId doesn't mess up (I'm going)
//since I want to keep searched text, Imma move this here
require "./search.phtml";
//normal max page count
$maxPage = getMaxPage($db, 10, $_GET["searchStr"]);//max page count
 


if (isset($_SESSION["user"]))$groups = listJoinedGroups($db, $_SESSION["user"]["userId"]);

if (isset($_POST["create"])){
    require "./somethingCreate.phtml";//opens form for inputs
}
    if (isset($_POST["finish"])){//if form filled, add to database and show
        //make the checkbox inputs into something usefull
        
        $addedGroupTags = [];
        foreach ($groups as $group){
            if (isset($_POST[("GROUP".$group["tagName"])])){
                array_push($addedGroupTags, $group["tagId"]);

            }

            //I HAVE NO IDEA HOW I FIXED THIS, BUT IT'S FIXED

        }
        var_dump($_POST);
        var_dump($addedGroupTags);
        
        createThing($db, $_SESSION["user"]["userId"],$_POST["thingText"], $_FILES["thingFile"], $_POST["thingTags"], $addedGroupTags);
    }
    
    //comments//
    
    //close them if not needed :)
        if (isset($_POST["closeComm"])) {
            unset($_GET["showComms"]);
        }
        //show comments
        if (isset($_GET["showComms"])){
            $thing = listSomething($db, false, null, $_GET["showComms"]);//slightly modified this to show comm thing aswell
            var_dump($thing);
            //add comment
            if (isset($_POST["addComm"])) createComm($db, $thing["thingId"], $_POST["commText"], $_FILES["commFile"]);
            $thingComms = getThingStuff($db, $_GET["showComms"], "comments");
            require "./somethingComms.phtml";
        }


        //show tag list
        else if (isset($_GET["tagList"])&&$_GET["tagList"]==="open"){
            $THINGTags = listTags($db, "THING");
            $USERTags = listTags($db, "USER");
            $GROUPTags = ListTags($db, "GROUP");
            require "./tagList.phtml";
        }


        //show things
        
        else {
            $things = listSomething($db, $_GET["searchStr"], $_GET["pageNum"]);
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
                        let switchForth = true   
                        document.getElementById("pageNumber").value++;//EASTER EGG - if you push button to next page while having maximum number of pages in input, you can get 1 over max
                    }
                    document.getElementById("pages").submit();
                })
            }
</script>
<div>

    <?php
foreach ($tagList as $tagInfo){//for each unique tag you found, add an eventListener to update searching
    ?>
<script>
    console.log(document.getElementsByClassName("<?="tag".$tagInfo["tagName"]?>"));
    let <?="tag".$tagInfo["tagName"]?> = document.getElementsByClassName("<?="tag".$tagInfo["tagName"]?>");
    for (let i=0; i< <?="tag".$tagInfo["tagName"]?>.length;i++){
        <?="tag".$tagInfo["tagName"]?>[i].addEventListener("click",()=>{
            document.getElementById("searchStr").value+="<?=" ".$tagInfo["tagType"].":".$tagInfo["tagName"]?>"
        })
        <?="tag".$tagInfo["tagName"]?>[i].addEventListener("mouseover",()=>{
            <?="tag".$tagInfo["tagName"]?>[i].children[1].style.visibility="visible";
        })
        <?="tag".$tagInfo["tagName"]?>[i].addEventListener("mouseleave",()=>{
            <?="tag".$tagInfo["tagName"]?>[i].children[1].style.visibility="hidden";
        })
    };
</script>

<?php
}
?>
</div>




