<?php


//I need a value that holds the string, and based on number of things it continuously filters the things
//first it selects things based on all THING tags, then GROUP and USER
//I should also add creator USER tags on things
//also tags of other people for fanart or something
function getMaxPage($db, $perPage=10, $tagSearch=null){
    if ($tagSearch){
        //transforms tagSearch and compares it with existing tags, then it puts it in a str and compares it with connections
        //just gonna copy this here to get all the necessary stuff
        $tags = explode(" ", $tagSearch);//get each tag separately in the form of an array
        //distribute the tags into their respective tagType
        $THING = [];
        $USER = [];
        $GROUP = [];
        $TEXT = [];
        //!!! THIS IS VULNERABLE TO PEOPLE ADDING THING:THING: OR SOMETHING OF THIS SORT, need to warn them
        foreach ($tags as $tag){//filter the tags
            if (strpos($tag, "THING:")!==false)array_push($THING, substr($tag, 6));//add tag to THING arr (ecerything after THING: so 6)
            if (strpos($tag, "USER:")!==false)array_push($USER, substr($tag, 5));//add tag to USER
            if (strpos($tag, "GROUP:")!==false)array_push($GROUP, substr($tag, 6));//add tag to GROUP
            if (strpos($tag, "TEXT:")!==false)array_push($TEXT, substr($tag, 5));//checks for TEXT tags (text found in things)
        }
        //joins everything and only shows, where there are ALL the tags... would be easier if I just got * from tags 
        $allTag =mysqli_fetch_all(mysqli_query($db, "SELECT * FROM tags"), MYSQLI_ASSOC);
    
        
        //searches through all the stuff to get the correct tag Ids
        $tagIds = [];
        foreach ($allTag as $compare){//each gotten tag
            if (array_search($compare["tagName"],$THING)!==false &&$compare["tagType"] ==="THING") array_push($tagIds, $compare["tagId"]);
            if (array_search($compare["tagName"],$USER)!==false &&$compare["tagType"] ==="USER") array_push($tagIds, $compare["tagId"]);
            if (array_search($compare["tagName"],$GROUP)!==false &&$compare["tagType"] ==="GROUP") array_push($tagIds, $compare["tagId"]);
        }
        
        $tagIds=implode(",", $tagIds);
        var_dump($tagIds);
        //I DONT WANNA GET ALL THINGTAG CONNECTIONS
        $thingtagStmt = mysqli_query($db, "
        SELECT thingtags.thingId, GROUP_CONCAT(DISTINCT thingtags.tagId ORDER BY thingtags.tagId ASC) AS thingTags
        FROM thingtags
        WHERE userId IS NULL
        GROUP BY thingtags.thingId
        ");
        $things=[];
        $Jeremy =mysqli_fetch_all($thingtagStmt, MYSQLI_ASSOC);
        foreach ($Jeremy as $Jim){
            var_dump($Jim);
            if (str_contains($Jim["thingTags"], $tagIds)) array_push($things, $Jim["thingId"]);
        }
        
        $countStmt = "
        SELECT COUNT(thingId) AS count FROM things
        WHERE thingId IN (".str_repeat("?, ", count($things)-1)."?)";
        
        
        return floor(mysqli_fetch_all(mysqli_execute_query($db, $countStmt, $things), MYSQLI_ASSOC)[0]["count"]/10);

        
        

    }
    $rest =mysqli_query($db, "SELECT COUNT(thingId) FROM things");
    $pages = mysqli_fetch_all($rest, MYSQLI_NUM);
    
    return floor($pages[0][0]/=$perPage);//returns amount of pages
}
//added thingId param for comments, since I don't want to make new function just for comments when I can add a HAVING clause
function listSomething($db, $tags, $pageNumber=null, $thingId=null){
    
    $finalStmt = "
    SELECT DISTINCT things.thingId, things.thingText, things.thingFile, things.FileIsWhat, things.thingAge, users.userId, users.userName, users.userDesc, users.userImage, users.userAge, users.userType , GROUP_CONCAT(DISTINCT thingtags.tagId) AS tagIds, GROUP_CONCAT(tags.tagName ORDER BY tags.tagId ASC) AS tagNames, GROUP_CONCAT(tags.tagDesc ORDER BY tags.tagId ASC) AS tagDescs, GROUP_CONCAT(tags.tagType ORDER BY tags.tagId ASC) AS tagTypes
    FROM `things`
    LEFT JOIN thingtags ON things.thingId = thingtags.thingId  /*LEFT JOIN on both thingtags and tags results in ALL THINGS YES WOO how did this take me 3 days to figure out*/
    JOIN users ON users.userId = things.userId
    LEFT JOIN tags ON thingtags.tagId = tags.tagId
    GROUP BY(things.thingId)
    /*IT WORKS, JUST EXPLODE tagId and tagNames and do a foreach on the resulting array*/
    ";

    //for comments only
    if (isset($thingId)){
        $finalStmt.="
        HAVING thingId=?;";
        $val = [$thingId];
        return mysqli_fetch_all(mysqli_execute_query($db, $finalStmt, $val), MYSQLI_ASSOC)[0];
    }

    if ($tags){//if searching for something, filter with tags (I should add thingText search aswell)
        $tags = explode(" ", $tags);//get each tag separately in the form of an array
        //distribute the tags into their respective tagType
        $THING = [];
        $USER = [];
        $GROUP = [];
        $TEXT = [];
        //!!! THIS IS VULNERABLE TO PEOPLE ADDING THING:THING: OR SOMETHING OF THIS SORT, need to warn them
        foreach ($tags as $tag){//filter the tags
            //searching might be annoying, so Imma keep this in :)
            if ($tag==="") continue;
            if (strpos($tag, "THING:")!==false)array_push($THING, substr($tag, 6));//add tag to THING arr (ecerything after THING: so 6)
            if (strpos($tag, "USER:")!==false)array_push($USER, substr($tag, 5));//add tag to USER
            if (strpos($tag, "GROUP:")!==false)array_push($GROUP, substr($tag, 6));//add tag to GROUP
            if (strpos($tag, "TEXT:")!==false)array_push($TEXT, substr($tag, 5));//checks for TEXT tags (text found in things)
        }
        
        //now for each group filter the results... somehow
        //option 1 - get all the things through $result and filter it in php with values gotten from tags table
        //option 2 - sql freak magic to filter it through
        //option 3 - dynamically add the values to a WHERE in the statement, gonna have to dynamically bind tho, which idk how to do

        //gets list of all tags and their tagTypes
        $tagstmt = mysqli_query($db, "
        SELECT tagId, tagName, tagType FROM tags
        ORDER BY tagId;
        ");
        $tagList = mysqli_fetch_all($tagstmt, MYSQLI_ASSOC);
        

        //!!! DOESN'T WORK, $filt
        //transforms the tags into a set of ids, to later search through thingtags
        $filt = [];
        foreach ($tagList as $tag){
            if ($tag["tagType"]==="THING"){
                if (array_search($tag["tagName"],$THING)!==false) array_push($filt, $tag["tagId"]);
            }
            if ($tag["tagType"]==="USER"){
                if (array_search($tag["tagName"],$USER)!==false) array_push($filt,$tag["tagId"]);
            }
            if ($tag["tagType"]==="GROUP"){
                if (array_search($tag["tagName"],$GROUP)!==false) array_push($filt,$tag["tagId"]);
            }
        }
        
        //so I have an array with tagIds wich I want to search for in thingTags (connections)
        //this means I have to perform a dynamic sql statement bind
        
        //create a statement with all relevant tagIds and their bind types
        //*FIXED: now gets things without a thingtags connection aswell when searching with TEXT:
        $filterStmt = "
            SELECT thingId AS thingId, GROUP_CONCAT(DISTINCT tagId ORDER BY tagId) AS tags
            FROM thingtags
            "
         ;
        if ($filt){
            $filterStmt=$filterStmt."WHERE tagId IN(".str_repeat("?, ", count($filt)-1)."?)";
        }
        $filterStmt =$filterStmt.   "
            GROUP BY thingId;
        ";
   
        //so far it checks the tagTypes, gets their Id and NOW I have to bind $filt values to a prepared statement

        //binds the the tagIds to the statement I REALLY HOPE IT WORKS LIKE THIS
        $tagRes = mysqli_execute_query($db, $filterStmt, $filt);
        //$tagRes =mysqli_execute_query($db, $filterStmt, $filt);
        //$filtDat = mysqli_fetch_all($tagRes, MYSQLI_NUM);

        $filtDat = mysqli_fetch_all($tagRes, MYSQLI_ASSOC); // fetch the data   


        if (count($filtDat)===0){//if nothing, with tags exit;
            echo "nothing with selected tag found :(";
            exit;
        }
        ////////////////////
//      ADD AN AND HERE STOOPID
        ////////////////////
        $AND = [];
        $tagstr =implode(",", $filt);
        foreach ($filtDat as $dat){
            if (str_contains($dat["tags"], $tagstr)) array_push($AND, $dat["thingId"]);
        }
        //var_dump($tagstr);
        if (count($AND)<1){
            echo "nothing with matching tags found :(";
            exit;
        }
        

        //adds filtering for things with specified tags
        $finalStmt = $finalStmt."HAVING things.thingId IN(".str_repeat(" ?,", count($AND)-1)." ?)";
    
        //adds further filtering for text in thingText (doesn't work for things without a thingtag connection)
        if (count($TEXT)!==0){
            foreach ($TEXT as $text){//in case there is more that one TEXT: search
                $text = "%$text%";//for LIKE statment - % means any number of chars, if you put it at end and start, you get searching for specific text in whole  strings of words
                array_push($AND, $text);//adds values to $finalDat array, which gets bound to $finalStmt
            }
            $finalStmt = "$finalStmt AND ".str_repeat("things.thingText LIKE ? OR", count($TEXT)-1)." things.thingText LIKE ?";//adds searching in text
        }

        //limits to pages
        $finalStmt="$finalStmt 
        LIMIT ? OFFSET ?";//adds LIMIT (limits to amount of rows retrieved) OFFSET(how many rows it skips and then applies LIMIT)
        array_push($AND, 10, $pageNumber*10);//adds corresponding values ()
       
        $quer =mysqli_execute_query($db, $finalStmt, $AND);
        $finalDat = mysqli_fetch_all($quer,MYSQLI_ASSOC);
        
        
        return $finalDat;

    }
    //gets every thing
    //limits to pages
    $filtDat=[11, 10*$pageNumber];
    $finalStmt= "$finalStmt
    LIMIT ? OFFSET ?";
    $result = mysqli_execute_query($db, $finalStmt, $filtDat);
    if ($result===false){
        echo "<h2> database retrieval failed </h2>";
        exit;
    }
    //returns it
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}














function createThing($db, $thingOwner, $thingText, $thingFile, $thingTags, $groupTags){
    //checks file
    $isWhat= null;
    $filePath = null;
    if ($thingFile["error"] !== UPLOAD_ERR_NO_FILE){
        if ($thingFile["error"] !== UPLOAD_ERR_OK){//is file and not ok
            echo "<h2> something wrong with loading file </h2>";
            return;
        }
        if(str_starts_with($thingFile["type"], "image/") === true){
            $isWhat="image";
        }
        if (!file_exists("someFiles")){//if no place for file
            mkdir("someFiles");
        }
        $filePath = "someFiles/".uniqid().$thingFile["name"];//WHY DELETE UNIQID BEFORE
        move_uploaded_file($thingFile["tmp_name"], $filePath);
    }

    //need to check if tags exist, if they do, continue, else make a new tag
    //need to prepare thingTags and make it into a new object
    $THING = "THING";
    $thingTags = explode(" ", $thingTags);
    foreach ($thingTags as $tag){// worried about the "AND"
        //I AM GONNA KEEP THIS OUT TO SHAME THE ONES WHO OVERTYPED AND ACCIDENTALLY PUT THE WRONG TAG THERE, also you can just change it so there isn't really any harm in it
        //if empy string, continue
        //if ($tag==="") continue;

        //also why not just use mysqli_bind if you can just do it with "." it's just a string?
        //something about 'tag' gonna try binding it aswell
        //turns out I would be opening my database to sql injections (very very bad and old vulnerability), so I guess I need to use bind params ;-;
        $tagStmt = mysqli_prepare($db, "
        SELECT * FROM tags
        WHERE tagName = ? AND tagType = 'THING';
        ");
        if ($tagStmt===false){
            echo "<h2> query failed</h2>";
            exit;
        }
        if (!mysqli_stmt_bind_param($tagStmt, "s", $tag)){
            echo "<h2> binding failed </h2>".mysqli_error($db);
            exit;
        }
        if(!mysqli_execute($tagStmt)){
            echo "<h2> tag select execution failed</h2>".mysqli_error($db);
            exit;
        }
        $tagi = mysqli_fetch_all(mysqli_stmt_get_result($tagStmt), MYSQLI_ASSOC);
        var_dump($tagi);
        if (!$tagi){//if it is empty, make new tag
            $tagStmt = mysqli_prepare($db, "
            INSERT INTO tags (tagName, tagDesc, tagType)
            VALUES (?, ?, ?)"
            );
            if ($tagStmt===false){//IT WORKS :D
                echo "<h2> tag preparation failed</h2>";
                echo mysqli_error($db);
                exit;
            }
            mysqli_stmt_bind_param($tagStmt, "sss", $tag, $tag, $THING);
            if (!mysqli_execute($tagStmt)){
                echo "<h2> tag execution failed</h2>";
                echo mysqli_error($db);
                exit;
            }//so far: checks if tag exists, if not adds it
        }//next: makes a connection in thingTags
        //NEED thingId for this...  could get it from querying for newest user thing? :< 
    }








    
    //creates thing
    $stmt = mysqli_prepare($db, "
        INSERT INTO things (userId, thingText, thingFile, FileIsWhat)
        VALUES (?, ?, ?, ?)
        ");
    if ($stmt === false) {
        echo "<h1> statement failed /h1>";
        echo mysqli_error($db);
        exit;
    }
    if (!mysqli_stmt_bind_param($stmt, "isss", $thingOwner, $thingText, $filePath, $isWhat)){//bind param is returning false ;-;
        echo "thing insert binding failed";
        echo mysqli_error($db);
        exit;
    }
    
    if (mysqli_execute($stmt) === false) {
        echo "<h1> execution failed </h1>";
        echo mysqli_error($db);
        exit;
    }
    
    //query which gets only latest thing(created right above)
    $query = mysqli_prepare($db, "
    SELECT * FROM things
    WHERE userId= ?
    ORDER BY thingId DESC
    LIMIT 1;
    ");
    if ($query===false){
        echo " query prep failed";
        exit;
    }
    if (!mysqli_stmt_bind_param($query, "i", $_SESSION["user"]["userId"])){
        echo mysqli_error($db);
        exit;
    };
    if (!mysqli_execute($query)){
        echo mysqli_error($db);
        exit;
    };
    $thingId = mysqli_fetch_all(mysqli_stmt_get_result($query), MYSQLI_ASSOC);
    var_dump($thingId);
    $thingId = $thingId[0]["thingId"];//gets the id of thing for connecting tags
    
    //get id of each THING tag used WORKS
    $tagLinks = [];
    foreach($thingTags as $tagName){
        $tig = mysqli_prepare($db, "
        SELECT * FROM tags
        WHERE tagName = ? AND tagType = ?;
        ");
        if (!mysqli_stmt_bind_param($tig, "ss", $tagName, $THING)){
            echo "get THING tagId bind param failed".mysqli_error($db);
            exit;
        }
        if (!mysqli_execute($tig)){
            echo "get tagId exec failed".mysqli_error($db);
            exit;
        }
        //this should get the id into an array for use :)
        array_push($tagLinks,mysqli_fetch_all(mysqli_stmt_get_result($tig), MYSQLI_ASSOC)[0]["tagId"]);
    }
    //get id of creator USER tag
    $USER = "USER";
    $userTag = mysqli_prepare($db, "
    SELECT * FROM tags
    WHERE tagName = ? AND tagType = ?");
    if (!mysqli_stmt_bind_param($userTag, "ss", $_SESSION["user"]["userName"], $USER)){
        echo "get USER tagId bind param failed".mysqli_error($db);
        exit;
    }
    if (!mysqli_execute($userTag)){
        echo "get USER tagId exec failed".mysqli_error($db);
        exit;
    }
    $USER = mysqli_fetch_all(mysqli_stmt_get_result($userTag), MYSQLI_ASSOC)[0]["tagId"];//IF IT WARNS YOU, THAT MEANS THE USER IS OLD AND DOESN'T HAVE A USER TAG
    if($USER)array_push($tagLinks, $USER);//adds creator USER tag to tagId array if said tag exists
    var_dump($tagLinks);




    //create tag connections
    foreach ($tagLinks as $tag){//I NEED tagId ASWELL ;_;
        $ins = mysqli_prepare($db, "
        INSERT INTO thingTags (thingId, tagId)
        VALUES (?, ?);
        ");
        if (!mysqli_stmt_bind_param($ins, "ii", $thingId, $tag)){
            echo "thingTag bind failed <br>".mysqli_error($db);
            exit;
        }
        if (!mysqli_execute($ins)){
            echo "<h2> thingTags execute error</h2>";
            echo mysqli_error($db);
            exit;
        }
    }//this SHOULD insert stuff based on thingId into thingTags... WE'LL SEE

    foreach ($groupTags as $tag){
        $ins = "
        INSERT INTO thingTags (thingId, tagId)
        VALUES (?, ?);";
        $vals = [$thingId, $tag];
        if (mysqli_execute_query($db, $ins, $vals)===false){
            echo "groupTag connection insert failed".mysqli_error($db);
            exit;
        }
        var_dump($groupTags);
    }
    var_dump($groupTags);


    //header("Location: /somethingig/something.php");
    
    
}

function removeThing($db, $thingId){
    if($_SESSION["user"]["userType"]!==null){
        $interference ="
        UPDATE things
        SET thingText = ?, thingFile = ?
        WHERE thingId = ?
        ";
        $interferenceVars = ["deleted by admin", null, $thingId];
        mysqli_execute_query($db, $interference, $interferenceVars);
        exit;
    }

    //REMEMBER first remove connections, then origin of connection, else you will be trying to remove a connection to nothing and sql doesn't like that
    $delTag = mysqli_prepare($db, "
    DELETE FROM thingtags
    WHERE thingId = ?
    ");//since execute and bind param return bools, it should be fine like this
    if (!mysqli_stmt_bind_param($delTag, "i", $thingId)){
        echo "<h2> thingTags del bind failed  </h2><br>".mysqli_error($db);
        exit;
    }
    if (!mysqli_execute($delTag)){
        echo "<h2> thingTags del exec failed  </h2><br>".mysqli_error($db);
        exit;
    }
    $stmt = mysqli_prepare($db, "
    DELETE FROM things
    WHERE thingId = ?
    ");
    if ($stmt === false){
        echo "<h2>statement failed</h2>";
        echo mysqli_error($db);
        exit;
    }
    if(!mysqli_stmt_bind_param($stmt, "i", $thingId)){
        echo "binding failed".mysqli_error($db);
        exit;
    }

    if (mysqli_execute($stmt) === false){
        echo "<h2> execution failed </h2>".mysqli_error($db);
        exit;
    }

    //cannot be here, because it sends an error and stops the remove function from working
    //header("Location: /somethingig/something.php");
    
}


function editThing($db ,$thingId , $thingText, $thingFile, $thingTags){//need to get thingtag connections with thingId and add/remove connections, aswell as adding tags when there isn't anything (I should've just used a string value in things to check for tags :/
    if ($thingTags){

        //array of tags    
        $tags =explode(" ", $thingTags);
        //array to get tags of type THING with $tags names
        $sel = $tags;
        $sel[count($tags)] = "THING";
        var_dump($sel);
        //gets id of tags mentioned
        $tagStmt = "
        SELECT tagId, tagName FROM tags
        WHERE tagName IN(".str_repeat("?, ", count($sel)-2)."?) AND tagType=?";
        $someTag =mysqli_execute_query($db, $tagStmt, $sel);
        $tagChecks=mysqli_fetch_all($someTag, MYSQLI_ASSOC);
        //gets tags which exist
        $newtag=[];
        foreach($tags as $tag){
            foreach ($tagChecks as $checks)
            if ($tag ===$checks["tagName"]) array_splice($tags, array_search($tag, $tags), 1);//removes from $tags, so I don't add new stuff that already exists
        }
                
            foreach ($tags as $tag){//for the remaining tags (the ones which don't have an existing tag in tags table) creates a new tag yippie :D
                $insert = [$tag, $tag,"THING"];
                $query = "
                INSERT INTO tags (tagName, tagDesc, tagType)
                VALUES (?, ?, ?);
                ";
                if (mysqli_execute_query($db, $query, $insert)===false){
                    echo "newTag insert failed";
                    exit;
                }    
            }
                
                
            
    
    //gets the new stuff ;-;, new tags included
    $someTag =mysqli_execute_query($db, $tagStmt, $sel);
    $tagChecks=mysqli_fetch_all($someTag, MYSQLI_ASSOC);
    
    //gets the thingtags connection of this thing
    $thingtaq = "
    SELECT * FROM thingtags
    WHERE thingId = ?";
    $thingtagId[0] = $thingId;//too lazy to write the whole bind_param and execute already :/
    $thingTagExec = mysqli_execute_query($db, $thingtaq, $thingtagId);
    $thingTagsFetch = mysqli_fetch_all($thingTagExec, MYSQLI_ASSOC);//  0=>("tagId"=>(id), "connectionId"=>(id))
    
    //compares it with the found and generated tags from $tagChecks
    //need to: update, insert, delete if too much 
    //////////////////////////////////////////////////////NOT DONE
    //get tags out of the fetch request array
    $connId = [];
    $connTag=[];
    foreach ($thingTagsFetch as $fetch){
        array_push($connId, $fetch["connectionId"]);
        array_push($connTag, $fetch["tagId"]);
    }
    //check for existing tags
    //I need to check for tags which are not there and leave them for insert, I also decrease the thingtags array by the ones, which don't need to be changed
    //so I check for things that don't need to be changed and remove them
    //I then check for thingtags, which can be UPDATEd and use them
    //I then INSERT them
    
    //removes existing correct connections from the further UPDATE and INSERT operations
    foreach($tagChecks as $tag){
        for ($i=0;$i<count($thingTagsFetch);$i++){
            if ($thingTagsFetch[$i]["tagId"]===$tag){
                array_splice($thingTagsFetch, $i, 1);
            } 
        }    
    }

    //now check for amount of tags that need UPDATE
    if (count($thingTagsFetch)>0){
        $UPDATE = "
        UPDATE thingtags
        SET tagId = ?
        WHERE connectionId = ?";
        
        //need to account for deleting tags from things...
        for ($i=0; $i<count($thingTagsFetch);){// for existing connections, updates them to new stuff
            if (!$tagChecks){//if I splice thingTagsFetch, then the for statement will be weird and probably not work
                echo "<br><br><br>tagChecks<br>";
                var_dump($tagChecks);
                $del = "
                DELETE FROM thingtags
                WHERE connectionId IN (".str_repeat("?, ", count($thingTagsFetch)-1)."?)";//ensures that I can delete everything with a single command
                $tet = [];
                foreach ($thingTagsFetch as $tat) array_push($tet, $tat["connectionId"]);
                echo "<br><br>DEL<br>";
                var_dump($tet);
                echo "<br><br>";
                if (mysqli_execute_query($db, $del, $tet)===false){
                    echo "thingtag delete leftovers query failed";
                    exit;    
                }
                break;//breaks the statement after deleting leftover connections :)
            } //if there aren't any $tagChecks left, but still in loop ($tagChecks<$thingTagsFetch), then delete the remaining connections
            $updt = [$tagChecks[0]["tagId"], $thingTagsFetch[0]["connectionId"]];
            echo "<br><br>updt<br>";
            var_dump($updt);
            echo "<br><br>";
            if (mysqli_execute_query($db, $UPDATE, $updt)===false){
                echo "thingtag UPDATE query failed";
                exit;
            }
            //this should ensure, that the values at 0 keep changing and if there 
            array_splice($thingTagsFetch, 0, 1);//removes the connectionId used from array acting as an "$i++", ensuring I can use the leftover thingTagsFetch to delete un needed connections :D
            array_splice($tagChecks, 0, 1);//removes the tag from array of tags needed to be added WOULDN'T SPLICE MOVE THE ARRAY BACK A BIT? 1,2,3 = 2,3
        }
        
    }
    
    //for the leftover tags (adding new tags to existing thing) it inserts them
    $tagInsert = "
    INSERT INTO thingTags (thingId, tagId)
    VALUES (?, ?)";
    foreach($tagChecks as $taggin){
        $insertTag = [$thingId, $taggin["tagId"]];
        mysqli_execute_query($db, $tagInsert, $insertTag);
        echo "<br> new connection :D<br>";
    }
}   
    
    



    //asks server for file with thingId
    $check = mysqli_prepare($db, "
    SELECT * 
    FROM things
    WHERE thingId = ?;
    ");
    mysqli_stmt_bind_param($check, "i", $thingId);
    if (!mysqli_execute($check)) {echo "<h2>execution failed</h2>";exit;}
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    
    //if file is not there, assume they wanted to keep the file
    //checks file if img, shows as image, if not makes download link
    $isWhat= null;
    $filePath = null;
    if ($thingFile["error"] === UPLOAD_ERR_NO_FILE) {
        $filePath=$row["thingFile"];
        $isWhat = $row["FileIsWhat"];
    }
    else{
        if ($thingFile["error"] !== UPLOAD_ERR_OK){//is file and not ok
            echo "<h2> something wrong with loading file </h2>";
            return;
        }
        if(str_starts_with($thingFile["type"], "image/") === true){
            $isWhat="image";
        }
        if (!file_exists("someFiles")){//if no place for file
            mkdir("someFiles");
        }
        $filePath = "someFiles/". $thingFile["name"];
        move_uploaded_file($thingFile["tmp_name"], $filePath);
    }
    //if no file given, keep old file
    //what if wants to delete file? just delete whole message.
    //what if wants to keep message, but delete file? COPY THE MESSAGE IT'S JUST TEXT
    //prepares statement and executes it
    $stmt = mysqli_prepare($db, "
    UPDATE things
    SET thingText = ?, thingFile = ?, FileIsWhat = ?
    WHERE thingId = ?;
    ");
    if ($stmt === false) {
        echo "<h1> statement failed /h1>";
        echo mysqli_error($db);
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "sssi", $thingText, $filePath, $isWhat, $thingId );
    $result = mysqli_execute($stmt);
    //var_dump($result);
    if ($result === false) {
        echo "<h1> execution failed </h1>";
        echo mysqli_error($db);
        exit;
    }
    header("Location: /somethingig/something.php");
    //return $row;
}

//gets stuff connected through $thingId from $table
function getThingStuff($db, $thingId, $table){
    $comque = "
    SELECT * FROM ".$table."
    JOIN users ON ".$table.".userId = users.userId
    WHERE thingId = ?;
    ";//IT DOESN'T WORK OTHERWISE, DON'T CHANGE YOU BASS
    $pars = [$thingId];
    $fetch =mysqli_execute_query($db, $comque, $pars);
    return mysqli_fetch_all($fetch, MYSQLI_ASSOC);
}

function createComm($db, $thingId, $commText, $commFile){
    //checks file
    $isWhat= null;
    $filePath = null;
    if ($commFile["error"] !== UPLOAD_ERR_NO_FILE){
        if ($commFile["error"] !== UPLOAD_ERR_OK){//is file and not ok
            echo "<h2> something wrong with loading file </h2>";
            return;
        }
        if(str_starts_with($commFile["type"], "image/") === true){
            $isWhat="image";
        }
        if (!file_exists("someFiles")){//if no place for file
            mkdir("someFiles");
        }
        $filePath = "someFiles/".uniqid().$commFile["name"];
        move_uploaded_file($commFile["tmp_name"], $filePath);
    }
    
    //prepares statement and executes it
    $stmt = "
        INSERT INTO comments (userId, thingId, comText, comFile, fileType)
        VALUES (?, ?, ?, ?, ?);
        ";
    $arr = [$_SESSION["user"]["userId"], $thingId, $commText, $filePath, $isWhat];
    if (mysqli_execute_query($db, $stmt, $arr)===false){
        echo "you did something wrong";
    };
}





?>